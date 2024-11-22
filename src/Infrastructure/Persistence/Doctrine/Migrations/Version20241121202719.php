<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241121202719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
        CREATE TABLE orders (
            id INT AUTO_INCREMENT NOT NULL,
            event_id INT NOT NULL,
            event_date VARCHAR(10) NOT NULL,
            equal_price INT NOT NULL,
            created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
    ');

        $this->addSql('
        CREATE TABLE tickets (
            id INT AUTO_INCREMENT NOT NULL,
            order_id INT NOT NULL,
            type ENUM(\'adult\', \'kid\', \'discounted\', \'group\') NOT NULL,
            price INT NOT NULL,
            quantity INT NOT NULL,
            PRIMARY KEY(id),
            CONSTRAINT FK_TICKETS_ORDER FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
    ');

        $this->addSql('
        CREATE TABLE ticket_barcodes (
            id INT AUTO_INCREMENT NOT NULL,
            ticket_id INT NOT NULL,
            barcode VARCHAR(120) NOT NULL UNIQUE,
            PRIMARY KEY(id),
            CONSTRAINT FK_BARCODES_TICKET FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
    ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE ticket_barcodes');
        $this->addSql('DROP TABLE tickets');
        $this->addSql('DROP TABLE orders');
    }
}
