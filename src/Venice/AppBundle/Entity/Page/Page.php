<?php

namespace Venice\AppBundle\Entity\Page;

class Page
{
    /*
     * Produkty, content, blog
     *
     * Produkt: vezme se z nej vsechen content
     * Content: bez uprav
     * Blog:
     *
     * moznost navazat na sebe tyto 3 entity se vztahy:
     *  1. spojit - obsah
     *
     */

    /** BLOG?
     * Co kdyby se blog prevedl na content?
     * Stejne uz ma spojeni s produktem a tim se resi access uzivatelu
     * Category by se pridal do blog content
     * Tags by mohly byt u vseho
     * Pak by bylo vse zobrazitelne Content a bylo by vse krapet jednodussi jak v praci s tim, tak i v datovem modelu.
     */


     /** STRANKY
      * Kazda stranka ma nazev a handle, podle ktereho se na ni lze dostat odkazem.
      * Nutno pridat nejakou moznost navigace mezi strankami:
      * 1. pridani stranek do menu(nektere tam byt musi, nektere ne)
      * 2. odkazy mezi strankami(ne jen jeden, ale je opet nutne moznost pridat odkaz tak,
      *     aby se stranky daly propojovat do libovolnych struktur, obecne do stromu)
      * 3. mozna pridani odkazu v ramci urovni stranek
      *     a. prejit na predchozi stranku stejne urovne, dalsi stranku stejne urovne, o uroven vyse, o uroven nize
      *
      * Datovy model
      *     Page, ktera by mela N:M vztah s Content. Vztah by byl povysen na entitu ContentOnPage.
      *     ContentOnPage by obsahoval order a delay, jako ContentProduct.
      *     V pripade, ze by jsme pozadovali ukladat vztahy mezi strankami, musel by vzniknout rekurzivni vztah M:M mezi entitou Page.
      *     Ten by byl opet povysen na entitu s dalsimi atributy vztahu(nadrazena, podrazena, na steje urovni).
      *
      * Vytvarelo by se to pomoci formularu. Pozdeji by mohl vzniknout drag&drop system.
      *
      * Zaroven by bylo hezke mit moznost editovat stranky primo tam, kde je vidi primo uzivatele(haha...
      *     pripadne pridat odkaz na kazdou stranku, ktery by admina presmeroval do administrace)
      *
      * Sandbox mod? Moznost editace stranek offline tak,  aby si je admin mohl prohlednout
      * a pripadne upravit pred tim, nez by tyto zmeny videli uzivatele.
      *
      * S implementaci stranek by pak ale ztratila vazba Product<->Content smysl.
      *
      * Vyhody:
      * 1. Vsechny blbosti v zobrazeni si muze admin naklikat sam
      *     napr. vsechny videa z flofitu chci sem, ale pred posledni 3 videa chci dat vsechny(2) videa z jineho produktu
      * 2. Silenost jako gotham, kde byly 3 typy entit(3 zanoreni), ktere se zobrazovaly.
      *
      * Nevyhody:
      * 1. vypocetne narocnejsi
      * 2. narocnejsi na pochopeni adminy
      * 3. slozitejsi na implementaci
      */


}