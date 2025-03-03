<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217040203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis ADD ref INT AUTO_INCREMENT NOT NULL, CHANGE id id_professional INT NOT NULL, ADD PRIMARY KEY (ref)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0907406D8 FOREIGN KEY (id_professional) REFERENCES user (ref)');
        $this->addSql('CREATE INDEX IDX_8F91ABF0907406D8 ON avis (id_professional)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC74B1B7F2 FOREIGN KEY (id_avis) REFERENCES avis (ref)');
        $this->addSql('CREATE INDEX IDX_5FB6DEC74B1B7F2 ON reponse (id_avis)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis MODIFY ref INT NOT NULL');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0907406D8');
        $this->addSql('DROP INDEX IDX_8F91ABF0907406D8 ON avis');
        $this->addSql('DROP INDEX `primary` ON avis');
        $this->addSql('ALTER TABLE avis DROP ref, CHANGE id_professional id INT NOT NULL');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC74B1B7F2');
        $this->addSql('DROP INDEX IDX_5FB6DEC74B1B7F2 ON reponse');
        $this->addSql('ALTER TABLE user CHANGE roles roles VARCHAR(255) NOT NULL');
    }
}
