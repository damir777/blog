<?php

namespace App\DataFixtures;

use App\Entity\ContentTaxon;
use App\Entity\Post;
use App\Entity\Taxon;
use App\Entity\TaxonBag;
use App\Entity\Taxonomy;
use App\Entity\User;
use App\EventSubscriber\OwnerHandler;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function __construct(private readonly OwnerHandler $ownerHandler)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->ownerHandler->disableSubscriber();

        $user1 = new User();
        $user1->setEmail('test1@gmail.com');
        $user1->setPassword('$2y$13$ohSJSL1dIBipl9hA2G.aKup4X53k0Fq7Pp8TxPjk846fLcpgO5Kni');
        $user1->setRoles(['ROLE_ADMIN']);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('test2@gmail.com');
        $user2->setPassword('$2y$13$ohSJSL1dIBipl9hA2G.aKup4X53k0Fq7Pp8TxPjk846fLcpgO5Kni');
        $manager->persist($user2);

        $taxonomy1 = new Taxonomy();
        $taxonomy1->setName('Tags taxonomy');
        $manager->persist($taxonomy1);

        $taxonomy2 = new Taxonomy();
        $taxonomy2->setName('Categories taxonomy');
        $manager->persist($taxonomy2);

        $taxonBag = new TaxonBag();
        $taxonBag->setTaxonomy($taxonomy1);
        $taxonBag->setName('Tags taxon bag');
        $manager->persist($taxonBag);

        for ($i = 1; $i <= 18; $i++) {
            $published = false;
            $createdUser = $user1;
            $updatedUser = $user2;

            if ($i % 2 === 0) {
                $published = true;
                $createdUser = $user2;
                $updatedUser = $user1;
            }

            $post = new Post();
            $post->setTitle('Post '.$i);
            $post->setBody('Example body for Post '.$i);
            $post->setPublished($published);
            $post->setCreatedBy($createdUser);
            $post->setUpdatedBy($updatedUser);
            $manager->persist($post);

            $taxon = new Taxon();
            $taxon->setTaxonomy($taxonomy1);
            $taxon->setTitle('Tag '.$i);
            $taxon->setCreatedBy($createdUser);
            $taxon->setUpdatedBy($updatedUser);
            $manager->persist($taxon);

            if ($i % 3 === 0) {
                $contentTaxon = new ContentTaxon();
                $contentTaxon->setTaxonBag($taxonBag);
                $contentTaxon->setTaxon($taxon);
                $contentTaxon->setPost($post);
                $contentTaxon->setCreatedBy($createdUser);
                $contentTaxon->setUpdatedBy($updatedUser);
                $manager->persist($contentTaxon);
            }
        }

        $manager->flush();
    }
}
