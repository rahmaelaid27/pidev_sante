<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217124622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE avis ADD ref INT AUTO_INCREMENT NOT NULL, CHANGE id professional_id INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (ref)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF06B3CA4B FOREIGN KEY (id_user) REFERENCES user (ref)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0DB77003 FOREIGN KEY (professional_id) REFERENCES user (ref)');
        $this->addSql('CREATE INDEX IDX_8F91ABF06B3CA4B ON avis (id_user)');
        $this->addSql('CREATE INDEX IDX_8F91ABF0DB77003 ON avis (professional_id)');
        $this->addSql('ALTER TABLE reponse ADD professional_id INT NOT NULL, CHANGE avis_id id_avis INT NOT NULL');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC74B1B7F2 FOREIGN KEY (id_avis) REFERENCES avis (ref) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7DB77003 FOREIGN KEY (professional_id) REFERENCES user (ref)');
        $this->addSql('CREATE INDEX IDX_5FB6DEC74B1B7F2 ON reponse (id_avis)');
        $this->addSql('CREATE INDEX IDX_5FB6DEC7DB77003 ON reponse (professional_id)');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL COMMENT \'(DC2Type:json)\', DROP role');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE avis MODIFY ref INT NOT NULL');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF06B3CA4B');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0DB77003');
        $this->addSql('DROP INDEX IDX_8F91ABF06B3CA4B ON avis');
        $this->addSql('DROP INDEX IDX_8F91ABF0DB77003 ON avis');
        $this->addSql('DROP INDEX `PRIMARY` ON avis');
        $this->addSql('ALTER TABLE avis DROP ref, CHANGE professional_id id INT NOT NULL');
        $this->addSql('ALTER TABLE avis ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC74B1B7F2');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7DB77003');
        $this->addSql('DROP INDEX IDX_5FB6DEC74B1B7F2 ON reponse');
        $this->addSql('DROP INDEX IDX_5FB6DEC7DB77003 ON reponse');
        $this->addSql('ALTER TABLE reponse ADD avis_id INT NOT NULL, DROP id_avis, DROP professional_id');
        $this->addSql('ALTER TABLE user ADD role VARCHAR(255) NOT NULL, DROP roles');
    }
}
