<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216221401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // If column name has changed, update accordingly
//        $this->addSql('DROP INDEX UNIQ_5FB6DEC7197E709F ON reponse');
//        $this->addSql('ALTER TABLE reponse CHANGE avis_id id_avis INT NOT NULL');  // Ensure column name is correct
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC74B1B7F2 FOREIGN KEY (id_avis) REFERENCES avis (ref)');
        $this->addSql('CREATE INDEX IDX_5FB6DEC74B1B7F2 ON reponse (id_avis)');
    }


    public function down(Schema $schema): void
    {
        // Drop the new foreign key
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC74B1B7F2');

        // Drop the new index
        $this->addSql('DROP INDEX IDX_5FB6DEC74B1B7F2 ON reponse');

        // Rename the column back to avis_id
        $this->addSql('ALTER TABLE reponse CHANGE id_avis avis_id INT NOT NULL');

        // Recreate the original foreign key
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7197E709F FOREIGN KEY (avis_id) REFERENCES avis (ref)');

        // Recreate the original unique index
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5FB6DEC7197E709F ON reponse (avis_id)');
    }
}