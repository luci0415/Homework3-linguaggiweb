Componenti del gruppo: 
Emilio Russo, Lucia Felici

Github:


Applicazione:
Quest'applicazione simula uno store virtuale di videogiochi. Funzionalità:
-Visualizzazione del catalogo per intero o in base a specifici filtri;
-Registrazione, login e logout;
-Aggiungere ed eliminare videogiochi dal carrello dell'utente;
-Procedere con l'acquisto dei videogiochi nel carrello, 
 con conseguente apparsa del resoconto dell'ordine e registrazione della ricevuta nel file xml.

L'applicazione fa uso di mysql per il lato database, HTML css e php per la struttura e l'estetica,
 e infine sfrutta XML e DOM per memorizzare informazioni più volatili quali sono i videogiochi nel carrello 
 (aumentano e diminuiscono più dinamicamente) e per registrare le corrispettive ricevute degli ordini effettuati.
Tramite DOM si aggiungono o tolgono nuovi nodi (i videogiochi) dal padre carrello e se ne modificano eventualmente
 i valori (quando l'utente aggiunge, nel carrello, una copia in più o una in meno del medesimo videogioco DOM
 cambia il valore dell'elemento "quantita").

L'applicazione dovrebbe essere avviata nel seguente modo:
1. All'interno della cartella è presente il file "dati-generali.php", il cui scopo è puramente contenere i parametri di accesso per il database.
   è necessario aprire il file e sostituire manualmente il nome utente e la password con quelli appropriati dell'utente che sta leggendo questo readme.
   Si utilizzi un utente che abbia i privilegi per creare e modificare tabelle all'interno del database, altrimenti non sarà possibile eseguire il programma.
2. Successivamente bisogna cercare il file "installa.php", il cui scopo è quello di popolare il database.
   L'idea è di eseguirlo <b>una sola volta</b>, altrimenti gli elementi vengono duplicati, triplicati...
3. Una volta eseguito l'"installa" si consiglia all'utente di accedere (in alto a destra) all'area utenti e registrarsi o fare il login. Nel codice 
   di "installa.php", alle righe 20-24 sono presenti degli utenti già registrati e la password associata se si vuole accedere direttamente con uno di quelli.
4. Una volta fatto l'accesso si può usufruire delle funzionalità del carrello.





