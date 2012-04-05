insert into school values ('1', '100000', '0');

insert into state values ('1', 'Nový');
insert into state values ('2', 'Schválený');
insert into state values ('3', 'Pripravený na realizáciu');
insert into state values ('4', 'V realizácii');
insert into state values ('5', 'Úspešne ukončený');
insert into state values ('6', 'Dočaste zastavený');
insert into state values ('7', 'Neúspešne ukončený');
insert into state values ('8', 'Zrušený');
insert into state values ('9', 'Zamietnutý');

insert into faculty (id, name, acronym) values ('1', 'Fakulta elektrotechniky a informatiky', 'FEI');
insert into faculty (id, name, acronym) values ('2', 'Fakulta informatiky a informačných technológií ', 'FIIT');
insert into faculty (id, name, acronym) values ('3', 'Materiálovotehnologická fakulta so sídlom v Trnave', 'MTF');
insert into faculty (id, name, acronym) values ('4', 'Fakulta architektúry', 'FA');
insert into faculty (id, name, acronym) values ('5', 'Fakulta chemickej a potravinárskej technológie', 'FCHPT');
insert into faculty (id, name, acronym) values ('6', 'Strojnícka fakulta', 'SjF');
insert into faculty (id, name, acronym) values ('7', 'Stavebná fakulta', 'SvF');

insert into institute (name, acronym, faculty_id) values ('Ústav elektroenergetiky a aplikovanej elektrotechniky', 'ÚEAE', '1');
insert into institute (name, acronym, faculty_id) values ('Ústav elektroniky a fotoniky', 'ÚEF', '1');
insert into institute (name, acronym, faculty_id) values ('ústav elektrotechniky', 'ÚE', '1');
insert into institute (name, acronym, faculty_id) values ('Ústav informatiky a matematiky', 'ÚIM', '1');
insert into institute (name, acronym, faculty_id) values ('Ústav jadrového a fyzikálneho inžinierstva', 'ÚJFI', '1');
insert into institute (name, acronym, faculty_id) values ('Ústav riadenia a priemyselnej informatiky', 'ÚRPI', '1');
insert into institute (name, acronym, faculty_id) values ('Ústav telekomunikácií', 'ÚT', '1');
insert into institute (name, acronym, faculty_id) values ('Katedra jazykov', 'KJ', '1');
insert into institute (name, acronym, faculty_id) values ('Katedra telesnej výchovy', 'KTV', '1');

insert into institute (name, acronym, faculty_id) values ('Ústav aplikovanej informatiky', 'ÚAPI', '2');
insert into institute (name, acronym, faculty_id) values ('Ústav informatiky a softvérového inžinierstva', 'ÚISI', '2');
insert into institute (name, acronym, faculty_id) values ('Ústav počítačových systémov a sietí', 'ÚPSS', '2');

insert into institute (name, acronym, faculty_id) values ('Ústav materiálov', 'ÚM', '3');
insert into institute (name, acronym, faculty_id) values ('Ústav výrobných technológií', 'ÚVT', '3');
insert into institute (name, acronym, faculty_id) values ('Ústav výrobných systémov a aplikovanej mechaniky', 'ÚVSAM', '3');
insert into institute (name, acronym, faculty_id) values ('Ústav aplikovanej informatiky, automatizácie a matematiky', 'ÚAIAM', '3');
insert into institute (name, acronym, faculty_id) values ('Ústav priemyselného inžinierstva, manažmentu a kvality', 'ÚPIMK', '3');
insert into institute (name, acronym, faculty_id) values ('Ústav bezpečnostného a enviromentálneho inžinierstva', 'ÚBEI', '3');
insert into institute (name, acronym, faculty_id) values ('Ústav inžinierskej pedagogiky a humanitných vied', 'ÚIPHV', '3');

insert into institute (name, acronym, faculty_id) values ('Ústav architektúry obytných budov', 'ÚAObyB', '4');
insert into institute (name, acronym, faculty_id) values ('Ústav architektúry občianskych budov', 'ÚAObčB', '4');
insert into institute (name, acronym, faculty_id) values ('Ústav ekologickej a experimentálnej architektúry', 'ÚEEA', '4');
insert into institute (name, acronym, faculty_id) values ('Ústav dejín a teórie architektúry a obnovy pamiatok', 'ÚDTAOP', '4');
insert into institute (name, acronym, faculty_id) values ('Ústav konštrukcií v architektúre a inžinierskych stavieb', 'ÚKAIS', '4');
insert into institute (name, acronym, faculty_id) values ('Ústav interiéru a výstavníctva', 'ÚIV', '4');
insert into institute (name, acronym, faculty_id) values ('Ústav urbanizmu a územného plánovania', 'ÚUÚP', '4');
insert into institute (name, acronym, faculty_id) values ('Ústav záhradnej a krajinnej architektúry', 'ÚZKA', '4');
insert into institute (name, acronym, faculty_id) values ('Ústav dizajnu', 'ÚD', '4');
insert into institute (name, acronym, faculty_id) values ('Kabinet počítačových a multimediálnych disciplín', 'KPMD', '4');
insert into institute (name, acronym, faculty_id) values ('Kabinet jazykov', 'KJ', '4');

insert into institute (name, acronym, faculty_id) values ('Ústav analytickej chémie', 'ÚACh', '5');
insert into institute (name, acronym, faculty_id) values ('Ústav anorganickej chémie, technológie a materiálov', 'ÚAChTM', '5');
insert into institute (name, acronym, faculty_id) values ('Ústav biochémie, výživy a ochrany zdravia', 'ÚBVOZ', '5');
insert into institute (name, acronym, faculty_id) values ('Ústav biotechnológie a potravinárstva', 'ÚBP', '5');
insert into institute (name, acronym, faculty_id) values ('Ústav fyzikálnej chémie a chemickej fyziky', 'ÚFChChF', '5');
insert into institute (name, acronym, faculty_id) values ('Ústav chemického a environmentálneho inžinierstva', 'ÚChEI', '5');
insert into institute (name, acronym, faculty_id) values ('Ústav informatizácie, automatizácie a matematiky', 'ÚIAM', '5');
insert into institute (name, acronym, faculty_id) values ('Ústav organickej chémie, katalýzy a petrochémie', 'ÚOChKP', '5');
insert into institute (name, acronym, faculty_id) values ('Ústav polymérnych materiálov', 'ÚPM', '5');
insert into institute (name, acronym, faculty_id) values ('Oddelenie jazykov', 'OJ', '5');
insert into institute (name, acronym, faculty_id) values ('Oddelenie telesnej výchovy a športu', 'OTVŠ', '5');

insert into institute (name, acronym, faculty_id) values ('Ústav aplikovanej mechaniky a mechatroniky', 'ÚAMM', '6');
insert into institute (name, acronym, faculty_id) values ('Ústav automatizácie, merania a aplikovanej informatiky', 'ÚAMAI', '6');
insert into institute (name, acronym, faculty_id) values ('Ústav dopravnej techniky a konštruovania', 'ÚDTK', '6');
insert into institute (name, acronym, faculty_id) values ('Ústav matematiky a fyziky', 'ÚMF', '6');
insert into institute (name, acronym, faculty_id) values ('Ústav chemických a hydraulických strojov a zariadení', 'ÚChHSZ', '6');
insert into institute (name, acronym, faculty_id) values ('Ústav technológií a materiálov', 'ÚTM', '6');
insert into institute (name, acronym, faculty_id) values ('Ústav tepelnej energetiky', 'ÚTE', '6');
insert into institute (name, acronym, faculty_id) values ('Ústav výrobných systémov, environmentálnej techniky a manažmentu kvality', 'ÚVSETMK', '6');
insert into institute (name, acronym, faculty_id) values ('Centrum jazykov a športu', 'CJŠ', '6');
insert into institute (name, acronym, faculty_id) values ('Centrum inovácií', 'CI', '6');
insert into institute (name, acronym, faculty_id) values ('Výpočtové a informačne stredisko', 'VIS', '6');

insert into institute (name, acronym, faculty_id) values ('Katedra betónových konštrukcií a mostov', 'KBKM', '7');
insert into institute (name, acronym, faculty_id) values ('Katedra dopravných stavieb', 'KDS', '7');
insert into institute (name, acronym, faculty_id) values ('Katedra geodetických základov', 'KGZ', '7');
insert into institute (name, acronym, faculty_id) values ('Katedra geodézie', 'KGd', '7');
insert into institute (name, acronym, faculty_id) values ('Katedra geotechniky', 'KGt', '7');
insert into institute (name, acronym, faculty_id) values ('Katedra vodného hospodárstva krajiny', 'KVHK', '7');
insert into institute (name, acronym, faculty_id) values ('Katedra hydrotechniky', 'KH', '7');
insert into institute (name, acronym, faculty_id) values ('Katedra konštrukcií pozemných stavieb', 'KKPS', '7');
insert into institute (name, acronym, faculty_id) values ('Katedra kovových a drevených konštrukcií', 'KKDK', '7');
insert into institute (name, acronym, faculty_id) values ('Katedra mapovania a pozemkových úprav', 'KMPÚ', '7');
insert into institute (name, acronym, faculty_id) values ('Katedra matematiky a deskriptívnej geometrie', 'KMDG', '7');
insert into institute (name, acronym, faculty_id) values ('Katedra fyziky', 'KF', '7');
insert into institute (name, acronym, faculty_id) values ('Katedra stavebnej mechaniky', 'KSM', '7');
insert into institute (name, acronym, faculty_id) values ('Katedra materiálového inžinierstva', 'KMI', '7');
insert into institute (name, acronym, faculty_id) values ('Katedra technológie stavieb', 'KTS', '7');
insert into institute (name, acronym, faculty_id) values ('Katedra zdravotného a environmentálneho inžinierstva', 'KZEI', '7');
insert into institute (name, acronym, faculty_id) values ('Katedra technických zariadení budov', 'KTZB', '7');
insert into institute (name, acronym, faculty_id) values ('Katedra jazykov', 'KJ', '7');
insert into institute (name, acronym, faculty_id) values ('Katedra telesnej výchovy', 'KTV', '7');
insert into institute (name, acronym, faculty_id) values ('Katedra humanitných vied', 'KHV', '7');
insert into institute (name, acronym, faculty_id) values ('Katedra architektúry', 'KA', '7');
insert into institute (name, acronym, faculty_id) values ('Ústav súdneho znalectva', 'ÚSZ', '7');


insert into user values ('1', '50774', 'Samuel', 'Kelemen');