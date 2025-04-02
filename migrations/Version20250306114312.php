<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250306114312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usuario ADD CONSTRAINT FK_2265B05DB239FBC6 FOREIGN KEY (institucion_id) REFERENCES institucion (id)');
        $this->addSql('CREATE INDEX IDX_2265B05DB239FBC6 ON usuario (institucion_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE estado DROP FOREIGN KEY FK_265DE1E37E3C61F9');
        $this->addSql('DROP INDEX IDX_265DE1E37E3C61F9 ON estado');
        $this->addSql('ALTER TABLE institucion CHANGE region_id region_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE usuario DROP FOREIGN KEY FK_2265B05DB239FBC6');
        $this->addSql('DROP INDEX IDX_2265B05DB239FBC6 ON usuario');
    }
}
