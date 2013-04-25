Menü Suite 1.0 von MyBBService.de
=================================

Verwaltet eure Headermenüs mit der Menü Suite von MyBBService
-------------------------------------------

### Installation
Wenn ihr das Plugin runtergeladen habt, entpackt das ZIP-File und ladet die Ordner admin und inc
in euren Forenroot. Also dorthin wo sich auch die index.php befindet.

Geht nun ins ACP und aktiviert das Plugin. Habt ihr alle Ordner und Dateien richtig hochgeladen,
seht ihr nun im ACP unter `Konfiguration` auf der linken Seite den Menüpunkt Menü Suite. Darüber
könnt ihr die Menüs für die jeweiligen Themes erstellen und verwalten. Die dortigen Funktionen sind
eigentlich selbsterklärend und gut beschrieben.

### Kurze Einführung in die Menü Suite
In der Regel erkennt und importiert unsere Menü Suite die vorhandenen Menüs der installierten Themes. Ausnahmen sind aber z.b. Drop Down Menüs. Diese werden derzeit noch nicht erkannt und können somit auch nicht mit der Menü Suite verwaltet werden. Ansonsten werden vorhandene Menüs automatisch von der Menü Suite importiert. Sollte der automatische Import mal nicht funktionieren, findet ihr auf MyBBService auch eine kurze Anleitung wie man in einem solchen Fall vorgehen muss.                        

Ist das Plugin aktiviert könnt ihr es im ACP unter Konfiguration-->Menü Suite aufrufen. Dort seht ihr dann den Inhalt der bereits importierten Headermenüs. Um euer Menü nun um einen weiteren Punkt zu ergänzen klickt einfach auf NEUEN PUNKT HINZUFÜGEN.

Im folgenden Fenster macht ihr dann bitte die Angaben wie folgt:

TITEL : Der Name eures neuen Menüpunkts, z.b. Impressum
LINK  : Hier machen wir 2 Angaben in den nächsten beiden Zeilen. In die erste Zeile kommt der Hyperlink zu dem verlinkt werden soll bei eurem Menüpunkt. In unserem Fall wäre das z.b. {$mybb->settings['bburl']}/impressum.php. Natürlich könnt ihr auch http://www.eurenlink.de angeben. Das ist alles machbar und ihr könnt das so machen wie ihr am besten zurecht kommt.

In der zweiten Zeile geben wir den Link zur Grafik an die für euren Link verwendet werden soll. Zum Beispiel: {$theme['imgdir']}/toplinks/euregrafik.gif. Natürlich müsst ihr die Grafik in das jeweilige Verzeichnis eures Themes hochladen damit sie angezeigt wird.

In den beiden Auswahlfeldern die dann noch angezeigt werden, könnt ihr festlegen in welchem Theme die Änderungen greifen sollen und welche Benutzergruppen den neu erstellten Menüpunkt sehen dürfen.

Das war soweit eine kleine Übersicht wie man unsere Menü Suite nutzt. Fragen, Anregungen wie immer auf MyBBService.de


### Wichtiger Hinweis
`Alle auf MyBBservice.de erhältlichen Plugins, Themes und Grafiken unterliegen dem Urheberrecht und
dürfen weder komplett oder auch nur auszugsweise - ohne schriftliches Einverständnis weitergegeben,
verkauft oder anderweitig verwendet bzw. veröffentlicht werden!
Zuwiderhandlungen werden strafrechtlich verfolgt! Support ausschließlich bei MyBBService.de.`