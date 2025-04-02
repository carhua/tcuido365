<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227121412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE persona CHANGE caso_desaparecido_total caso_desaparecido_total SMALLINT DEFAULT 0, CHANGE caso_desproteccion_total caso_desproteccion_total SMALLINT DEFAULT 0, CHANGE caso_trata_total caso_trata_total SMALLINT DEFAULT 0, CHANGE caso_violencia_total caso_violencia_total SMALLINT DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE caso_desaparecido ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, DROP codigo, DROP usuario_caso');
        $this->addSql('ALTER TABLE caso_desproteccion ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, DROP codigo, DROP usuario_caso');
        $this->addSql('ALTER TABLE caso_trata ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, DROP codigo, DROP usuario_caso');
        $this->addSql('ALTER TABLE caso_violencia ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, DROP codigo, DROP usuario_caso');
        $this->addSql('ALTER TABLE persona CHANGE caso_desaparecido_total caso_desaparecido_total SMALLINT DEFAULT NULL, CHANGE caso_desproteccion_total caso_desproteccion_total SMALLINT DEFAULT NULL, CHANGE caso_trata_total caso_trata_total SMALLINT DEFAULT NULL, CHANGE caso_violencia_total caso_violencia_total SMALLINT DEFAULT NULL');
    }
}
