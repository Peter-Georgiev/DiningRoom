<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191009113922 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment CHANGE products_id products_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product CHANGE students_id students_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE student CHANGE classes_id classes_id INT DEFAULT NULL, CHANGE teachers_id teachers_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE teacher ADD full_name VARCHAR(255) NOT NULL, DROP first_name, DROP middle_name, DROP last_name');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment CHANGE products_id products_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product CHANGE students_id students_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE student CHANGE classes_id classes_id INT DEFAULT NULL, CHANGE teachers_id teachers_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE teacher ADD middle_name VARCHAR(255) NOT NULL COLLATE utf8_general_ci, ADD last_name VARCHAR(255) NOT NULL COLLATE utf8_general_ci, CHANGE full_name first_name VARCHAR(255) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
