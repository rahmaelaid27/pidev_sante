<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to update the foreign key constraint for `reponse` table
 */
final class Version20250217110705 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update foreign key constraint for reponse table to add ON DELETE CASCADE';
    }

    // Version20250217110705.php
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC74B1B7F2');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC74B1B7F2 FOREIGN KEY (id_avis) REFERENCES avis (ref) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC74B1B7F2');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC74B1B7F2 FOREIGN KEY (id_avis) REFERENCES avis (ref)');
    }

}
