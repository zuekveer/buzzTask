<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241121202719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create orders, tickets, and ticket_barcodes tables with all required columns and relationships.';
    }

    public function up(Schema $schema): void
    {
        // Create orders table
        $this->addSql('
        CREATE TABLE orders (
            id INT AUTO_INCREMENT NOT NULL,
            event_id INT NOT NULL,
            event_date VARCHAR(10) NOT NULL,
            ticket_adult_price INT NOT NULL,
            ticket_adult_quantity INT NOT NULL,
            ticket_kid_price INT NOT NULL,
            ticket_kid_quantity INT NOT NULL,
            equal_price INT NOT NULL,
            barcode VARCHAR(120) NOT NULL UNIQUE,  -- Added barcode column
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
        ');

        // Create tickets table with VARCHAR instead of ENUM
        $this->addSql('
        CREATE TABLE tickets (
            id INT AUTO_INCREMENT NOT NULL,
            order_id INT DEFAULT NULL,
            type VARCHAR(255) NOT NULL,
            price INT NOT NULL,
            quantity INT NOT NULL,
            PRIMARY KEY(id),
            CONSTRAINT FK_TICKETS_ORDER FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
        ');

        // Create ticket_barcodes table
        $this->addSql('
        CREATE TABLE ticket_barcodes (
            id INT AUTO_INCREMENT NOT NULL,
            ticket_id INT DEFAULT NULL,
            barcode VARCHAR(120) NOT NULL UNIQUE,
            PRIMARY KEY(id),
            CONSTRAINT FK_BARCODES_TICKET FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
        ');
    }

    public function down(Schema $schema): void
    {
        // Drop tables in reverse order
        $this->addSql('DROP TABLE ticket_barcodes');
        $this->addSql('DROP TABLE tickets');
        $this->addSql('DROP TABLE orders');
    }
}
