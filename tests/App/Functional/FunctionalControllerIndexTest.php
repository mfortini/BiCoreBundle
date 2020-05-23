<?php

namespace App\Tests\Controller;

use Cdf\BiCoreBundle\Tests\Utils\BiTestAuthorizedClient;

class FunctionalControllerIndexTest extends BiTestAuthorizedClient
{
    public function testFunctionalClienteIndex()
    {
        $clientiregistrati = 15;
        $htmltableid = 'tableCliente';
        $client = static::createPantherClient();
        $testUrl = '/Cliente/';
        $crawler = $client->request('GET', $testUrl);
        $client->waitFor('#'.$htmltableid); // Wait for the tabellaCliente to appear
        $this->assertSame(self::$baseUri.$testUrl, $client->getCurrentURL()); // Assert we're still on the same page
        $clienti = $crawler->filterXPath('//table[@id="'.$htmltableid.'"]')->filter('tbody')->filter('tr')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $i) {
                return trim($td->text());
            });
        });
        $this->assertSame($clientiregistrati, count($clienti));

        sleep(1);
        $this->clickElement('.bibottonimodificatabellaCliente[data-biid="1"]');
        $client->waitFor('a.h-100.d-flex.align-items-center.btn.btn-xs.btn-primary');
        $this->clickElement('a.h-100.d-flex.align-items-center.btn.btn-xs.btn-primary');

        $client->waitFor('#ClienteSubTabellaDettagliContainer');
        $this->clickElement('.bibottonimodificatabellaOrdine[data-biid="2"]');
        $client->waitFor('a.h-100.d-flex.align-items-center.btn.btn-xs.btn-primary');
        $this->clickElement('a.h-100.d-flex.align-items-center.btn.btn-xs.btn-primary');

        $selectorinputqta = 'tr[data-bitableid=\"2\"] > td[data-nomecampo="Ordine.quantita"] :input';
        $selectorconfirm = '#tableOrdine > tbody:nth-child(2) > tr:nth-child(2) > td:nth-child(6) > a:nth-child(2)';

        $qta1ex = 11;

        sleep(1);
        $this->executeScript("document.querySelector('#tableOrdine > tbody > tr:nth-child(2) > td:nth-child(4) > div > input').value=".$qta1ex);

        sleep(1);
        $this->clickElement($selectorconfirm);

        sleep(1);

        $qta2ex = 10;

        $this->clickElement('.bibottonimodificatabellaOrdine[data-biid="2"]');
        $client->waitFor('a.h-100.d-flex.align-items-center.btn.btn-xs.btn-primary');
        $this->clickElement('a.h-100.d-flex.align-items-center.btn.btn-xs.btn-primary');

        $this->executeScript("document.querySelector('#tableOrdine > tbody > tr:nth-child(2) > td:nth-child(4) > div > input').value=".$qta2ex);

        sleep(1);
        $this->clickElement($selectorconfirm);
        sleep(1);

        //Problema su salvataggio perchè non trovo un pulsante di conferma univoco per la pagina che riceverebbe il click
        $this->clickElement('#card-simple2-tab');
        sleep(1);

        $this->clickElement('.bibottonimodificatabellaMagazzino[data-biid="3"]');
        $client->waitFor('a.h-100.d-flex.align-items-center.btn.btn-xs.btn-primary');
        $this->clickElement('a.h-100.d-flex.align-items-center.btn.btn-xs.btn-primary');

        $selectorconfirm = 'a.bibottonieditinline[data-biid="3"][data-tabella="Magazzino"]';

        $this->selectFieldOption('Magazzino_giornodellasettimana', 3);
        sleep(1);
        $this->clickElement($selectorconfirm);

        sleep(1);

        $this->clickElement('.bibottonimodificatabellaMagazzino[data-biid="3"]');
        $client->waitFor('a.h-100.d-flex.align-items-center.btn.btn-xs.btn-primary');
        $this->clickElement('a.h-100.d-flex.align-items-center.btn.btn-xs.btn-primary');

        $this->selectFieldOption('Magazzino_giornodellasettimana', 2);
        sleep(1);
        sleep(1);
        $this->clickElement($selectorconfirm);
        sleep(1);
        $this->logout();
    }

    public function testFunctionalFornitoreIndex()
    {
        $fornitoriregistrati = 3;
        $htmltableid = 'tableFornitore';
        $client = static::createPantherClient();
        $testUrl = '/Fornitore/';
        $crawler = $client->request('GET', $testUrl);
        $client->waitFor('#'.$htmltableid); // Wait for the tabellaFornitore to appear
        $this->assertSame(self::$baseUri.$testUrl, $client->getCurrentURL()); // Assert we're still on the same page
        $fornitori = $crawler->filterXPath('//table[@id="'.$htmltableid.'"]')->filter('tbody')->filter('tr')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $i) {
                return trim($td->text());
            });
        });
        $this->assertSame($fornitoriregistrati, count($fornitori));
        $this->logout();
    }

    public function testSecuredFunctionalMagazzinoIndex()
    {
        $url = $this->getRoute('Magazzino_container');
        $client = static::createPantherClient();

        $client->request('GET', $url);
        $client->waitFor('.tabellasearch');

        $this->pressButton('.tabellasearch');
        $fieldhtml = 'html body div.tabella-container div#tabellaMagazzino div#TabMagazzinoContent.tab-content div#tabMagazzino1a.tab-pane.p-3.fade.active.show div div.panel.panel-primary.filterable table#tableMagazzino.table.table-sm.table-responsive-sm.table-striped.bitable.table-hover thead tr.filters.d-flex th.biw-19 input.form-control.colonnatabellafiltro';
        $this->fillField($fieldhtml, 'ed');

        $this->findField($fieldhtml)->sendKeys("\n");
        sleep(5);
        $crawler = new \Symfony\Component\DomCrawler\Crawler($this->getCurrentPageContent());
        $numrowstabella = $crawler->filterXPath('//table[@id="tableMagazzino"]')->filter('tbody')->filter('tr')->count();
        $this->assertEquals(5, $numrowstabella);

        $this->pressButton('birimuovifiltriMagazzino');
        sleep(5);
        $crawler = new \Symfony\Component\DomCrawler\Crawler($this->getCurrentPageContent());
        $numrowstabella = $crawler->filterXPath('//table[@id="tableMagazzino"]')->filter('tbody')->filter('tr')->count();
        $this->assertEquals(11, $numrowstabella);

        $this->logout();
    }

    public function testFunctionalOrdineIndex()
    {
        $ordiniregistrati = 14;
        $htmltableid = 'tableOrdine';
        $client = static::createPantherClient();
        $testUrl = '/Ordine/';
        $crawler = $client->request('GET', $testUrl);
        $client->waitFor('#'.$htmltableid); // Wait for the tabellaOrdine to appear
        $this->assertSame(self::$baseUri.$testUrl, $client->getCurrentURL()); // Assert we're still on the same page
        $ordini = $crawler->filterXPath('//table[@id="'.$htmltableid.'"]')->filter('tbody')->filter('tr')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $i) {
                return trim($td->text());
            });
        });
        $this->assertSame($ordiniregistrati, count($ordini));
        $this->logout();
    }

    public function testFunctionalProdottofornitoreIndex()
    {
        $prodottifornitoreregistrati = 11;
        $htmltableid = 'tableProdottofornitore';
        $client = static::createPantherClient();
        $testUrl = '/Prodottofornitore/';
        $crawler = $client->request('GET', $testUrl);
        $client->waitFor('#'.$htmltableid); // Wait for the tabellaProdottofornitore to appear
        $this->assertSame(self::$baseUri.$testUrl, $client->getCurrentURL()); // Assert we're still on the same page
        $prodottifornitore = $crawler->filterXPath('//table[@id="'.$htmltableid.'"]')->filter('tbody')->filter('tr')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $i) {
                return trim($td->text());
            });
        });
        $this->assertSame($prodottifornitoreregistrati, count($prodottifornitore));
        $this->logout();
    }
}
