<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217005335 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON avis');
        $this->addSql('ALTER TABLE avis CHANGE id ref INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE avis ADD PRIMARY KEY (ref)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC74B1B7F2 FOREIGN KEY (id_avis) REFERENCES avis (ref)');
        $this->addSql('CREATE INDEX IDX_5FB6DEC74B1B7F2 ON reponse (id_avis)');
        $this->addSql('ALTER TABLE user CHANGE role roles VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis MODIFY ref INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON avis');
        $this->addSql('ALTER TABLE avis CHANGE ref id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE avis ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC74B1B7F2');
        $this->addSql('DROP INDEX IDX_5FB6DEC74B1B7F2 ON reponse');
        $this->addSql('ALTER TABLE user CHANGE roles role VARCHAR(255) NOT NULL');
    }
}
