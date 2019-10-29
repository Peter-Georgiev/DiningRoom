<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191002165801 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADCB944F1A');
        $this->addSql('DROP INDEX IDX_D34A04ADCB944F1A ON product');
        $this->addSql('ALTER TABLE product ADD students_id INT DEFAULT NULL, ADD month DATE NOT NULL, DROP student_id, CHANGE date_purchases date_purchases DATETIME NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD1AD8D010 FOREIGN KEY (students_id) REFERENCES student (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD1AD8D010 ON product (students_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD1AD8D010');
        $this->addSql('DROP INDEX IDX_D34A04AD1AD8D010 ON product');
        $this->addSql('ALTER TABLE product ADD student_id INT DEFAULT NULL, DROP students_id, DROP month, CHANGE date_purchases date_purchases DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADCB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADCB944F1A ON product (student_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
