<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230913141216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transport_en_commun DROP FOREIGN KEY FK_92C156201E4A7B3A');
        $this->addSql('ALTER TABLE transport_en_commun ADD CONSTRAINT FK_92C156201E4A7B3A FOREIGN KEY (type_transport_id) REFERENCES type_transport (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transport_en_commun DROP FOREIGN KEY FK_92C156201E4A7B3A');
        $this->addSql('ALTER TABLE transport_en_commun ADD CONSTRAINT FK_92C156201E4A7B3A FOREIGN KEY (type_transport_id) REFERENCES type_transport (id)');
    }
}
