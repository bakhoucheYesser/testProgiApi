<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241003000837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE association_fees (id INT AUTO_INCREMENT NOT NULL, min_price DOUBLE PRECISION NOT NULL, max_price DOUBLE PRECISION NOT NULL, association_fee DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE calculations (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, vehicle_type_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', base_price DOUBLE PRECISION NOT NULL, basic_fee DOUBLE PRECISION NOT NULL, special_fee DOUBLE PRECISION NOT NULL, association_fee DOUBLE PRECISION NOT NULL, storage_fee DOUBLE PRECISION NOT NULL, total_price DOUBLE PRECISION NOT NULL, INDEX IDX_4BFD195EA76ED395 (user_id), INDEX IDX_4BFD195EDA3FD1FC (vehicle_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE settings (id INT AUTO_INCREMENT NOT NULL, setting_key VARCHAR(255) NOT NULL, value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vehicule_type (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', name VARCHAR(255) NOT NULL, basic_fee_min DOUBLE PRECISION NOT NULL, basic_fee_max DOUBLE PRECISION NOT NULL, basic_fee_rate DOUBLE PRECISION NOT NULL, special_fee_rate DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE calculations ADD CONSTRAINT FK_4BFD195EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE calculations ADD CONSTRAINT FK_4BFD195EDA3FD1FC FOREIGN KEY (vehicle_type_id) REFERENCES vehicule_type (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calculations DROP FOREIGN KEY FK_4BFD195EA76ED395');
        $this->addSql('ALTER TABLE calculations DROP FOREIGN KEY FK_4BFD195EDA3FD1FC');
        $this->addSql('DROP TABLE association_fees');
        $this->addSql('DROP TABLE calculations');
        $this->addSql('DROP TABLE settings');
        $this->addSql('DROP TABLE vehicule_type');
    }
}
