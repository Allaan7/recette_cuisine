<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241023133115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire ADD recette_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC89312FE9 FOREIGN KEY (recette_id) REFERENCES recette (id)');
        $this->addSql('CREATE INDEX IDX_67F068BC89312FE9 ON commentaire (recette_id)');
        $this->addSql('ALTER TABLE recette DROP FOREIGN KEY FK_49BB6390BA9CD190');
        $this->addSql('DROP INDEX IDX_49BB6390BA9CD190 ON recette');
        $this->addSql('ALTER TABLE recette DROP commentaire_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC89312FE9');
        $this->addSql('DROP INDEX IDX_67F068BC89312FE9 ON commentaire');
        $this->addSql('ALTER TABLE commentaire DROP recette_id');
        $this->addSql('ALTER TABLE recette ADD commentaire_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE recette ADD CONSTRAINT FK_49BB6390BA9CD190 FOREIGN KEY (commentaire_id) REFERENCES commentaire (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_49BB6390BA9CD190 ON recette (commentaire_id)');
    }
}
