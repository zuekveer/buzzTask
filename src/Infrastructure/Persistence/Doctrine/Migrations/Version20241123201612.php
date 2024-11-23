<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241123201612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add total_price column to the orders table';
    }

    public function up(Schema $schema): void
    {
        // Add the total_price column to the orders table
        $this->addSql('ALTER TABLE orders ADD total_price DECIMAL(10, 2) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // Remove the total_price column from the orders table
        $this->addSql('ALTER TABLE orders DROP COLUMN total_price');
    }
}
