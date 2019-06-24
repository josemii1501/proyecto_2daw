<?php declare(strict_types=1);

namespace AppBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190624085117 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, video_id INT DEFAULT NULL, file VARCHAR(255) NOT NULL, date DATETIME NOT NULL, extension VARCHAR(255) NOT NULL, nombre VARCHAR(255) NOT NULL, INDEX IDX_8C9F361029C1004E (video_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE suscription (id INT AUTO_INCREMENT NOT NULL, suscriptor_id INT DEFAULT NULL, chanel_id INT DEFAULT NULL, timestamp DATETIME NOT NULL, INDEX IDX_CD871086C7CD703B (suscriptor_id), INDEX IDX_CD87108626F4971E (chanel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE saved (id INT AUTO_INCREMENT NOT NULL, usuario_id INT DEFAULT NULL, video_id INT DEFAULT NULL, timestamp DATETIME NOT NULL, INDEX IDX_F9E3D325DB38439E (usuario_id), INDEX IDX_F9E3D32529C1004E (video_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, photo VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_64C19C15E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usuario (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, birthday DATE NOT NULL, login VARCHAR(255) NOT NULL, clave VARCHAR(255) NOT NULL, avatar VARCHAR(255) NOT NULL, url_web_site VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, publisher TINYINT(1) NOT NULL, admin TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_2265B05DE7927C74 (email), UNIQUE INDEX UNIQ_2265B05DAA08CB10 (login), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE history (id INT AUTO_INCREMENT NOT NULL, usuario_id INT DEFAULT NULL, video_id INT DEFAULT NULL, timestamp DATETIME NOT NULL, INDEX IDX_27BA704BDB38439E (usuario_id), INDEX IDX_27BA704B29C1004E (video_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE video (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, category_id INT DEFAULT NULL, route VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date DATETIME NOT NULL, reproductions INT NOT NULL, miniature VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_7CC7DA2C2C42079 (route), INDEX IDX_7CC7DA2C61220EA6 (creator_id), INDEX IDX_7CC7DA2C12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F361029C1004E FOREIGN KEY (video_id) REFERENCES video (id)');
        $this->addSql('ALTER TABLE suscription ADD CONSTRAINT FK_CD871086C7CD703B FOREIGN KEY (suscriptor_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE suscription ADD CONSTRAINT FK_CD87108626F4971E FOREIGN KEY (chanel_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE saved ADD CONSTRAINT FK_F9E3D325DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE saved ADD CONSTRAINT FK_F9E3D32529C1004E FOREIGN KEY (video_id) REFERENCES video (id)');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704BDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704B29C1004E FOREIGN KEY (video_id) REFERENCES video (id)');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C61220EA6 FOREIGN KEY (creator_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2C12469DE2');
        $this->addSql('ALTER TABLE suscription DROP FOREIGN KEY FK_CD871086C7CD703B');
        $this->addSql('ALTER TABLE suscription DROP FOREIGN KEY FK_CD87108626F4971E');
        $this->addSql('ALTER TABLE saved DROP FOREIGN KEY FK_F9E3D325DB38439E');
        $this->addSql('ALTER TABLE history DROP FOREIGN KEY FK_27BA704BDB38439E');
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2C61220EA6');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F361029C1004E');
        $this->addSql('ALTER TABLE saved DROP FOREIGN KEY FK_F9E3D32529C1004E');
        $this->addSql('ALTER TABLE history DROP FOREIGN KEY FK_27BA704B29C1004E');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE suscription');
        $this->addSql('DROP TABLE saved');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE usuario');
        $this->addSql('DROP TABLE history');
        $this->addSql('DROP TABLE video');
    }
}
