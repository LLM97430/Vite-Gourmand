<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261031095657 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE commande DROP id, CHANGE numero_commande numero_commande INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (numero_commande)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande MODIFY numero_commande INT NOT NULL');
        $this->addSql('ALTER TABLE commande ADD id INT AUTO_INCREMENT NOT NULL, CHANGE numero_commande numero_commande VARCHAR(50) NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }
}
