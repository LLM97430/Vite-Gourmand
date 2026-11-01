<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261031115625 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur_role DROP FOREIGN KEY `FK_9EE8E650D60322AC`');
        $this->addSql('ALTER TABLE utilisateur_role DROP FOREIGN KEY `FK_9EE8E650FB88E14F`');
        $this->addSql('DROP TABLE utilisateur_role');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE utilisateur_role (utilisateur_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_9EE8E650D60322AC (role_id), INDEX IDX_9EE8E650FB88E14F (utilisateur_id), PRIMARY KEY (utilisateur_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE utilisateur_role ADD CONSTRAINT `FK_9EE8E650D60322AC` FOREIGN KEY (role_id) REFERENCES role (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur_role ADD CONSTRAINT `FK_9EE8E650FB88E14F` FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
