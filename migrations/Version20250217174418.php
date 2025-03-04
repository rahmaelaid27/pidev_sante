<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217174418 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF06B3CA4B');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0DB77003');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF06B3CA4B FOREIGN KEY (id_user) REFERENCES user (ref)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0DB77003 FOREIGN KEY (professional_id) REFERENCES user (ref)');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC74B1B7F2');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7DB77003');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC74B1B7F2 FOREIGN KEY (id_avis) REFERENCES avis (ref)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7DB77003 FOREIGN KEY (professional_id) REFERENCES user (ref)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF06B3CA4B');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0DB77003');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF06B3CA4B FOREIGN KEY (id_user) REFERENCES user (ref) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0DB77003 FOREIGN KEY (professional_id) REFERENCES user (ref) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC74B1B7F2');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7DB77003');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC74B1B7F2 FOREIGN KEY (id_avis) REFERENCES avis (ref) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7DB77003 FOREIGN KEY (professional_id) REFERENCES user (ref) ON DELETE CASCADE');
    }
}
