<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217132640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE consultation (id INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, id_professionnel INT NOT NULL, date_consultation DATE DEFAULT NULL, INDEX IDX_964685A66B3CA4B (id_user), INDEX IDX_964685A6C400106A (id_professionnel), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prescription (id INT AUTO_INCREMENT NOT NULL, id_consultation INT NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_1FBFB8D9B587F0D4 (id_consultation), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rendez_vous (id INT AUTO_INCREMENT NOT NULL, consultation_id INT NOT NULL, date_rdv DATE NOT NULL, status_rdv VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_65E8AA0A62FF6CDF (consultation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A66B3CA4B FOREIGN KEY (id_user) REFERENCES user (ref)');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A6C400106A FOREIGN KEY (id_professionnel) REFERENCES user (ref)');
        $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D9B587F0D4 FOREIGN KEY (id_consultation) REFERENCES consultation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0A62FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A66B3CA4B');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A6C400106A');
        $this->addSql('ALTER TABLE prescription DROP FOREIGN KEY FK_1FBFB8D9B587F0D4');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0A62FF6CDF');
        $this->addSql('DROP TABLE consultation');
        $this->addSql('DROP TABLE prescription');
        $this->addSql('DROP TABLE rendez_vous');
    }
}
