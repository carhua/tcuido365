<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250224180100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE caso_desaparecido ADD latitud VARCHAR(50) DEFAULT NULL, ADD longitud VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE caso_desproteccion ADD latitud VARCHAR(50) DEFAULT NULL, ADD longitud VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE caso_violencia ADD latitud VARCHAR(50) DEFAULT NULL, ADD longitud VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE caso_desaparecido DROP latitud, DROP longitud');
        $this->addSql('ALTER TABLE caso_desproteccion DROP latitud, DROP longitud');
        $this->addSql('ALTER TABLE caso_violencia DROP latitud, DROP longitud');
    }
}
