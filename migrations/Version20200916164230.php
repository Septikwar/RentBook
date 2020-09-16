<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200916164230 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE rent_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE rent (id INT NOT NULL DEFAULT nextval(\'rent_id_seq\'), book_id INT DEFAULT NULL, renter_id INT DEFAULT NULL, quantity INT NOT NULL, sum DOUBLE PRECISION NOT NULL, PRIMARY KEY(id), days INT NOT NULL)');
        $this->addSql('CREATE INDEX IDX_2784DCC71868B2E ON rent (book_id)');
        $this->addSql('CREATE INDEX IDX_2784DCC5704F9D1 ON rent (renter_id)');
        $this->addSql('ALTER TABLE rent ADD CONSTRAINT FK_2784DCC71868B2E FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rent ADD CONSTRAINT FK_2784DCC5704F9D1 FOREIGN KEY (renter_id) REFERENCES renter (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE rent_id_seq CASCADE');
        $this->addSql('DROP TABLE rent');
    }
}
