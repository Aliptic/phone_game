<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210607080942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE static_page (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        $textMention = "\"<h2>Identité</h2><p>Nom du site web : Le jeu du téléphone<br>Adresse : https://localhost:8080<br>Propriétaire : StartUp Nation™ Corp<br>Responsable de publication : StartUp Nation™ Corp<br>Conception et réalisation : StartUp Nation™ Corp<br>Hébergement : Docker<br><h2>Conditions d’utilisation</h2><p>L’utilisation du présent site implique l’acceptation pleine et entière des conditions générales d’utilisation décrites ci-après. Ces conditions d’utilisation sont susceptibles d’être modifiées ou complétées à tout moment.<h2>Informations</h2><p>Les informations et documents du site sont présentés à titre indicatif, n’ont pas de caractère exhaustif, et ne peuvent engager la responsabilité du propriétaire du site.<p>Le propriétaire du site ne peut être tenu responsable des dommages directs et indirects consécutifs à l’accès au site.<h2>Interactivité</h2><p>Les utilisateurs du site peuvent y déposer du contenu, apparaissant sur le site dans des espaces dédiés (notamment via les commentaires). Le contenu déposé reste sous la responsabilité de leurs auteurs, qui en assument pleinement l’entière responsabilité juridique.<p>Le propriétaire du site se réserve néanmoins le droit de retirer sans préavis et sans justification tout contenu déposé par les utilisateurs qui ne satisferait pas à la charte déontologique du site ou à la législation en vigueur.<h2>Propriété intellectuelle</h2><p>Sauf mention contraire, tous les éléments accessibles sur le site (textes, images, graphismes, logo, icônes, sons, logiciels, etc.) restent la propriété exclusive de leurs auteurs, en ce qui concerne les droits de propriété intellectuelle ou les droits d’usage.<p>Toute reproduction, représentation, modification, publication, adaptation de tout ou partie des éléments du site, quel que soit le moyen ou le procédé utilisé, est interdite, sauf autorisation écrite préalable de l’auteur.23<p>Toute exploitation non autorisée du site ou de l’un quelconque des éléments qu’il contient est considérée comme constitutive d’une contrefaçon et poursuivie.<p>Les marques et logos reproduits sur le site sont déposés par les sociétés qui en sont propriétaires.<h2>Liens</h2><h3>Liens sortants</h3><p>Le propriétaire du site décline toute responsabilité et n’est pas engagé par le référencement via des liens hypertextes, de ressources tierces présentes sur le réseau Internet, tant en ce qui concerne leur contenu que leur pertinence.<h3>Liens entrants</h3><p>Le propriétaire du site autorise les liens hypertextes vers l’une des pages de ce site, à condition que ceux-ci ouvrent une nouvelle fenêtre et soient présentés de manière non équivoque afin d’éviter :<ul><li>tout risque de confusion entre le site citant et le propriétaire du site<li>ainsi que toute présentation tendancieuse, ou contraire aux lois en vigueur.</ul><p>Le propriétaire du site se réserve le droit de demander la suppression d’un lien s’il estime que le site source ne respecte pas les règles ainsi définies.<h2>Confidentialité</h2><p>Tout utilisateur dispose d’un droit d’accès, de rectification et d’opposition aux données personnelles le concernant, en effectuant sa demande écrite et signée, accompagnée d’une preuve d’identité.<p>Le site ne recueille pas d’informations personnelles, et n’est pas assujetti à déclaration à la CNIL.<h2>Crédits</h2>StartUp Nation™ Corp\"";
        $this->addSql("INSERT INTO static_page (id, title, text) VALUES (1, 'mentions' , $textMention)");
        
        $textTeam = "\"<div><div><h1>L'équipe</h1><p>L'équipe de travail à été formée avec pour objectif le développement d'un jeu à l'aide de PHP grâce au Framework Symfony au sein de la formation Aliptic</div><h2>Les membres</h2><div><section><h1>Ronan</h1><img src=https://fakeimg.pl/100/ ><p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Omnis sed vitae aliquam. Harum commodi accusamus, neque repellat voluptatum quibusdam quisquam!</section><section><h1>Quentin</h1><img src=https://fakeimg.pl/100/ ><p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Voluptas reprehenderit asperiores a perspiciatis atque voluptatibus dolor itaque temporibus explicabo blanditiis.</section><section><h1>Tony</h1><img src=https://fakeimg.pl/100/ ><p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Commodi fugiat doloribus doloremque quidem excepturi tenetur labore fugit quam mollitia recusandae!</section></div></div>\"";
        $this->addSql("INSERT INTO static_page (id, title, text) VALUES (2, 'team' , $textTeam)");
        
        $textRules = "\"<style>.example-wrapper{margin:1em auto;max-width:800px;width:95%;font:18px/1.5 sans-serif}.example-wrapper code{background:#F5F5F5;padding:2px 6px}</style><div class=example-wrapper><h1>Règles du jeu</h1><ol><li>Créer une partie et inviter des amis, si t’en as pas, tu peux aussi jouer en solo.<li>Chaque joueur doit écrire une phrase, bizarre tant qu’à faire et si tu es à court d'inspiration, on y a pensé.<li>A tes pinceaux. Avec la phrase affichée, fait ta plus belle œuvre.<li>Quelle phrase t'inspire ce beau dessin ?<li>Allez, un récap pour voir où c'est parti en couille.</ol></div>\"";
        $this->addSql("INSERT INTO static_page (id, title, text) VALUES (3, 'rules' , $textRules )");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE static_page');
    }
}
