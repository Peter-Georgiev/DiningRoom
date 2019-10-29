<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191002113623 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE student ADD class_id INT NOT NULL, ADD teacher_id INT NOT NULL');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33EA000B10 FOREIGN KEY (class_id) REFERENCES class_table (id)');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF3341807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
        $this->addSql('CREATE INDEX IDX_B723AF33EA000B10 ON student (class_id)');
        $this->addSql('CREATE INDEX IDX_B723AF3341807E1D ON student (teacher_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33EA000B10');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF3341807E1D');
        $this->addSql('DROP INDEX IDX_B723AF33EA000B10 ON student');
        $this->addSql('DROP INDEX IDX_B723AF3341807E1D ON student');
        $this->addSql('ALTER TABLE student DROP class_id, DROP teacher_id');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
