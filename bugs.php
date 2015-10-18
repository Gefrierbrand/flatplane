Flatplane Bugs

rechter Header (Subsection) wird z.T. zu spät gesetzt (v.a. bei Pagebreaks)
Absatztrennung für Quelltexte fehlerhaft (falsche farben, ggf TCPDF Bug)
erster Seiteninhalt muss Pagebreak = false haben
Fußnoten sind noch buggy: Kein Verzeichnis von Fußnoten mögl? Nur auf der definierten Seite angezeigt usw
Titelseite & Content sind zwingend nötig
enumerate nur im constructor setzbar
Titelseite alleine hat Footer
Header auf titelseite ggf falsch (siehe MA, muss manuell deaktiviert werden?)
label kann nicht im Constructor gesetzt werden
größenangaben images buggy, v.a. wenn SVGs verwendet werden und übergröße vorliegt. zudem: beide params müssen angegeben sein & gesetzte größe !=  ausgabegröße wegen caption
Umbruch und position der IMG desc z.T. fehlerhaft
keine Links für Tabellenverz & Quelltextverz
tabelle ignoriert fußnoten?
startsNewPage istbuggy, falls element nicht angezeigt iwrd
leerer text kann nicht hinzugefügt werden
minfreespace der sections scheint nicht zu funktionieren
differenzen zwischen startpos & akt.pos?
objektdimensionen wegen ignoretopmargin z.T. nach pagebreak falsch
fußnoten nutzen getstringheight obwohl htmlcontent drin ist-> problematisch!
margins für section per level haben andere bezeichnung als sonst
viele angaben benötigen arrays (pagebreak, font etc)
text wird ggf über anderen text platziert (bsp: code nach pagebreak?)
svg-png probleme
image bottom margin muss oft manuell auf 10 gesetzt werden? (kein umbruch nach dem text?)
keine zugriffsmöglichkeit auf PDF funktionen bei der dokumenterstellung für seitenzahl margins etc
textcache hat einfluss auf fußnotenpositionierung (WTF!?)
pfad zu config funktioniert nicht, falls php nicht im servermodus ist bzw nicht im path liegt!!!
Fußnotengröße wird nicht zurückgesetzt!

kopfzeile fehlt, falls erstes element (z.b. liste) der seite an dokument und nicht an section hängt
seitenzahlen falsch?
doppelte titelseite?