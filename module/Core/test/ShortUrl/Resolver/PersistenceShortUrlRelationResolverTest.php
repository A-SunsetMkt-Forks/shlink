<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Core\ShortUrl\Resolver;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\Core\Entity\Domain;
use Shlinkio\Shlink\Core\Entity\Tag;
use Shlinkio\Shlink\Core\Repository\TagRepositoryInterface;
use Shlinkio\Shlink\Core\ShortUrl\Resolver\PersistenceShortUrlRelationResolver;
use function count;

class PersistenceShortUrlRelationResolverTest extends TestCase
{
    use ProphecyTrait;

    private PersistenceShortUrlRelationResolver $resolver;
    private ObjectProphecy $em;

    public function setUp(): void
    {
        $this->em = $this->prophesize(EntityManagerInterface::class);
        $this->resolver = new PersistenceShortUrlRelationResolver($this->em->reveal());
    }

    /** @test */
    public function returnsEmptyWhenNoDomainIsProvided(): void
    {
        $getRepository = $this->em->getRepository(Domain::class);

        self::assertNull($this->resolver->resolveDomain(null));
        $getRepository->shouldNotHaveBeenCalled();
    }

    /**
     * @test
     * @dataProvider provideFoundDomains
     */
    public function findsOrCreatesDomainWhenValueIsProvided(?Domain $foundDomain, string $authority): void
    {
        $repo = $this->prophesize(ObjectRepository::class);
        $findDomain = $repo->findOneBy(['authority' => $authority])->willReturn($foundDomain);
        $getRepository = $this->em->getRepository(Domain::class)->willReturn($repo->reveal());

        $result = $this->resolver->resolveDomain($authority);

        if ($foundDomain !== null) {
            self::assertSame($result, $foundDomain);
        }
        self::assertInstanceOf(Domain::class, $result);
        self::assertEquals($authority, $result->getAuthority());
        $findDomain->shouldHaveBeenCalledOnce();
        $getRepository->shouldHaveBeenCalledOnce();
    }

    public function provideFoundDomains(): iterable
    {
        $authority = 'doma.in';

        yield 'not found domain' => [null, $authority];
        yield 'found domain' => [new Domain($authority), $authority];
    }

    /**
     * @test
     * @dataProvider provideTags
     */
    public function findsAndPersistsTagsWrappedIntoCollection(array $tags, array $expectedTags): void
    {
        $expectedPersistedTags = count($expectedTags);

        $tagRepo = $this->prophesize(TagRepositoryInterface::class);
        $findTag = $tagRepo->findOneBy(Argument::type('array'))->will(function (array $args): ?Tag {
            ['name' => $name] = $args[0];
            return $name === 'foo' ? new Tag($name) : null;
        });
        $getRepo = $this->em->getRepository(Tag::class)->willReturn($tagRepo->reveal());
        $persist = $this->em->persist(Argument::type(Tag::class));

        $result = $this->resolver->resolveTags($tags);

        self::assertCount($expectedPersistedTags, $result);
        self::assertEquals($expectedTags, $result->toArray());
        $findTag->shouldHaveBeenCalledTimes($expectedPersistedTags);
        $getRepo->shouldHaveBeenCalledOnce();
        $persist->shouldHaveBeenCalledTimes($expectedPersistedTags);
    }

    public function provideTags(): iterable
    {
        yield 'no duplicated tags' => [['foo', 'bar', 'baz'], [new Tag('foo'), new Tag('bar'), new Tag('baz')]];
        yield 'duplicated tags' => [['foo', 'bar', 'bar'], [new Tag('foo'), new Tag('bar')]];
    }

    /** @test */
    public function returnsEmptyCollectionWhenProvidingEmptyListOfTags(): void
    {
        $tagRepo = $this->prophesize(TagRepositoryInterface::class);
        $findTag = $tagRepo->findOneBy(Argument::type('array'))->willReturn(null);
        $getRepo = $this->em->getRepository(Tag::class)->willReturn($tagRepo->reveal());
        $persist = $this->em->persist(Argument::type(Tag::class));

        $result = $this->resolver->resolveTags([]);

        self::assertEmpty($result);
        $findTag->shouldNotHaveBeenCalled();
        $getRepo->shouldNotHaveBeenCalled();
        $persist->shouldNotHaveBeenCalled();
    }
}
