<?php

namespace App\Tests\Controller;

use App\Entity\RendezVous;
use App\Repository\RendezVousRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class RendezVousControllerTest extends WebTestCase{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $rendezVouRepository;
    private string $path = '/rendez/vous/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->rendezVouRepository = $this->manager->getRepository(RendezVous::class);

        foreach ($this->rendezVouRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('RendezVou index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'rendez_vou[id_patient]' => 'Testing',
            'rendez_vou[date_rendez_vous]' => 'Testing',
            'rendez_vou[statu_paiement]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->rendezVouRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new RendezVous();
        $fixture->setId_patient('My Title');
        $fixture->setDate_rendez_vous('My Title');
        $fixture->setStatu_paiement('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('RendezVou');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new RendezVous();
        $fixture->setId_patient('Value');
        $fixture->setDate_rendez_vous('Value');
        $fixture->setStatu_paiement('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'rendez_vou[id_patient]' => 'Something New',
            'rendez_vou[date_rendez_vous]' => 'Something New',
            'rendez_vou[statu_paiement]' => 'Something New',
        ]);

        self::assertResponseRedirects('/rendez/vous/');

        $fixture = $this->rendezVouRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getId_patient());
        self::assertSame('Something New', $fixture[0]->getDate_rendez_vous());
        self::assertSame('Something New', $fixture[0]->getStatu_paiement());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new RendezVous();
        $fixture->setId_patient('Value');
        $fixture->setDate_rendez_vous('Value');
        $fixture->setStatu_paiement('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/rendez/vous/');
        self::assertSame(0, $this->rendezVouRepository->count([]));
    }
}
