<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210503151846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE IF EXISTS participant_sortir');
        $this->addSql('DROP TABLE IF EXISTS sortir');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE participant_sortir (participant_id INT NOT NULL, sortir_id INT NOT NULL, INDEX IDX_D90E8B49D1C3019 (participant_id), INDEX IDX_D90E8B41BCB675 (sortir_id), PRIMARY KEY(participant_id, sortir_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sortir (id INT AUTO_INCREMENT NOT NULL, lieu_id INT NOT NULL, campus_id INT NOT NULL, participant_id INT NOT NULL, etatlib_id INT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_heure_debut DATE NOT NULL, duree INT NOT NULL, date_limite_inscription DATE NOT NULL, nb_inscription_max INT NOT NULL, infos_sortie VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, etat VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_BFEC56359E569831 (etatlib_id), INDEX IDX_BFEC5635AF5D55E1 (campus_id), INDEX IDX_BFEC56359D1C3019 (participant_id), INDEX IDX_BFEC56356AB213CC (lieu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
    }
}
