<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191003200917 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, products_id INT DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, payment NUMERIC(10, 2) NOT NULL, date_purchases DATETIME NOT NULL, edit_date DATETIME NOT NULL, is_paid TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_6D28840D6C8A81A9 (products_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D6C8A81A9 FOREIGN KEY (products_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product CHANGE students_id students_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE student CHANGE classes_id classes_id INT DEFAULT NULL, CHANGE teachers_id teachers_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE payment');
        $this->addSql('ALTER TABLE product CHANGE students_id students_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE student CHANGE classes_id classes_id INT DEFAULT NULL, CHANGE teachers_id teachers_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
