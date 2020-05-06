<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200506111153 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE field (id INT AUTO_INCREMENT NOT NULL, building_id INT NOT NULL, offset_x INT NOT NULL, offset_y INT NOT NULL, phase INT NOT NULL, planted TINYINT(1) NOT NULL, INDEX IDX_5BF545584D2A7E12 (building_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE farm (id INT AUTO_INCREMENT NOT NULL, player_id INT NOT NULL, farm_index INT NOT NULL, INDEX IDX_5816D04599E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, player_id INT NOT NULL, pid INT NOT NULL, amount INT NOT NULL, INDEX IDX_D34A04AD99E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE building (id INT AUTO_INCREMENT NOT NULL, farm_id INT NOT NULL, position INT NOT NULL, type INT NOT NULL, INDEX IDX_E16F61D465FCFA0D (farm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF545584D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE farm ADD CONSTRAINT FK_5816D04599E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD99E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D465FCFA0D FOREIGN KEY (farm_id) REFERENCES farm (id)');
        $this->addSql('DROP TABLE cookies');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D465FCFA0D');
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF545584D2A7E12');
        $this->addSql('CREATE TABLE cookies (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE field');
        $this->addSql('DROP TABLE farm');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE building');
    }
}
