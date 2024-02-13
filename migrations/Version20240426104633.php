<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240426104633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE content_taxa (id INT AUTO_INCREMENT NOT NULL, taxon_bag_id INT NOT NULL, taxon_id INT NOT NULL, post_id INT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9C3D063565E4B6E6 (taxon_bag_id), INDEX IDX_9C3D0635DE13F470 (taxon_id), INDEX IDX_9C3D06354B89032C (post_id), INDEX IDX_9C3D0635B03A8386 (created_by_id), INDEX IDX_9C3D0635896DBBDE (updated_by_id), UNIQUE INDEX unique_content_taxon_idx (taxon_bag_id, taxon_id, post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE posts (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, body LONGTEXT DEFAULT NULL, published TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_885DBAFA989D9B62 (slug), INDEX IDX_885DBAFAB03A8386 (created_by_id), INDEX IDX_885DBAFA896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE taxa (id INT AUTO_INCREMENT NOT NULL, taxonomy_id INT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT NOT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_515FEBF09557E6F6 (taxonomy_id), INDEX IDX_515FEBF0B03A8386 (created_by_id), INDEX IDX_515FEBF0896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE taxon_bags (id INT AUTO_INCREMENT NOT NULL, taxonomy_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_E64C1B135E237E06 (name), INDEX IDX_E64C1B139557E6F6 (taxonomy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE taxonomies (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_232B80F95E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE content_taxa ADD CONSTRAINT FK_9C3D063565E4B6E6 FOREIGN KEY (taxon_bag_id) REFERENCES taxon_bags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE content_taxa ADD CONSTRAINT FK_9C3D0635DE13F470 FOREIGN KEY (taxon_id) REFERENCES taxa (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE content_taxa ADD CONSTRAINT FK_9C3D06354B89032C FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE content_taxa ADD CONSTRAINT FK_9C3D0635B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE content_taxa ADD CONSTRAINT FK_9C3D0635896DBBDE FOREIGN KEY (updated_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFAB03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFA896DBBDE FOREIGN KEY (updated_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE taxa ADD CONSTRAINT FK_515FEBF09557E6F6 FOREIGN KEY (taxonomy_id) REFERENCES taxonomies (id)');
        $this->addSql('ALTER TABLE taxa ADD CONSTRAINT FK_515FEBF0B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE taxa ADD CONSTRAINT FK_515FEBF0896DBBDE FOREIGN KEY (updated_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE taxon_bags ADD CONSTRAINT FK_E64C1B139557E6F6 FOREIGN KEY (taxonomy_id) REFERENCES taxonomies (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE content_taxa DROP FOREIGN KEY FK_9C3D063565E4B6E6');
        $this->addSql('ALTER TABLE content_taxa DROP FOREIGN KEY FK_9C3D0635DE13F470');
        $this->addSql('ALTER TABLE content_taxa DROP FOREIGN KEY FK_9C3D06354B89032C');
        $this->addSql('ALTER TABLE content_taxa DROP FOREIGN KEY FK_9C3D0635B03A8386');
        $this->addSql('ALTER TABLE content_taxa DROP FOREIGN KEY FK_9C3D0635896DBBDE');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFAB03A8386');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFA896DBBDE');
        $this->addSql('ALTER TABLE taxa DROP FOREIGN KEY FK_515FEBF09557E6F6');
        $this->addSql('ALTER TABLE taxa DROP FOREIGN KEY FK_515FEBF0B03A8386');
        $this->addSql('ALTER TABLE taxa DROP FOREIGN KEY FK_515FEBF0896DBBDE');
        $this->addSql('ALTER TABLE taxon_bags DROP FOREIGN KEY FK_E64C1B139557E6F6');
        $this->addSql('DROP TABLE content_taxa');
        $this->addSql('DROP TABLE posts');
        $this->addSql('DROP TABLE taxa');
        $this->addSql('DROP TABLE taxon_bags');
        $this->addSql('DROP TABLE taxonomies');
        $this->addSql('DROP TABLE users');
    }
}
