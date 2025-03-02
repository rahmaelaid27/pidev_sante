<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217014810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (ref INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(ref)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE avis MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON avis');
        $this->addSql('ALTER TABLE avis CHANGE id ref INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF06B3CA4B FOREIGN KEY (id_user) REFERENCES user (ref)');
        $this->addSql('ALTER TABLE avis ADD PRIMARY KEY (ref)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC74B1B7F2 FOREIGN KEY (id_avis) REFERENCES avis (ref)');
        $this->addSql('CREATE INDEX IDX_5FB6DEC74B1B7F2 ON reponse (id_avis)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF06B3CA4B');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE avis MODIFY ref INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON avis');
        $this->addSql('ALTER TABLE avis CHANGE ref id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE avis ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC74B1B7F2');
        $this->addSql('DROP INDEX IDX_5FB6DEC74B1B7F2 ON reponse');
    }
}
