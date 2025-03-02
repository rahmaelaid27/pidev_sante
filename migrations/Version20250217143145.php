<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217143145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0DB77003');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0DB77003 FOREIGN KEY (professional_id) REFERENCES user (ref) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7DB77003');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7DB77003 FOREIGN KEY (professional_id) REFERENCES user (ref) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0DB77003');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0DB77003 FOREIGN KEY (professional_id) REFERENCES user (ref)');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7DB77003');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7DB77003 FOREIGN KEY (professional_id) REFERENCES user (ref)');
    }
}
