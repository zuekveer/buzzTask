<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241123203408 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders ADD ticket_vip_price INT NOT NULL, ADD ticket_vip_quantity INT NOT NULL');
        $this->addSql('ALTER TABLE ticket_barcodes DROP FOREIGN KEY FK_BARCODES_TICKET');
        $this->addSql('ALTER TABLE ticket_barcodes ADD CONSTRAINT FK_3A2159E5700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id)');
        $this->addSql('ALTER TABLE ticket_barcodes RENAME INDEX barcode TO UNIQ_3A2159E597AE0266');
        $this->addSql('ALTER TABLE ticket_barcodes RENAME INDEX fk_barcodes_ticket TO IDX_3A2159E5700047D2');
        $this->addSql('ALTER TABLE tickets RENAME INDEX fk_54469df48d9f6d38 TO IDX_54469DF48D9F6D38');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders DROP ticket_vip_price, DROP ticket_vip_quantity');
        $this->addSql('ALTER TABLE tickets RENAME INDEX idx_54469df48d9f6d38 TO FK_54469DF48D9F6D38');
        $this->addSql('ALTER TABLE ticket_barcodes DROP FOREIGN KEY FK_3A2159E5700047D2');
        $this->addSql('ALTER TABLE ticket_barcodes ADD CONSTRAINT FK_BARCODES_TICKET FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ticket_barcodes RENAME INDEX uniq_3a2159e597ae0266 TO barcode');
        $this->addSql('ALTER TABLE ticket_barcodes RENAME INDEX idx_3a2159e5700047d2 TO FK_BARCODES_TICKET');
    }
}
