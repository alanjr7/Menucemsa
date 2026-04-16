<?php

namespace Database\Seeders;

use App\Models\AlmacenMedicamento;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AlmacenMedicamentoSeeder extends Seeder
{
    private array $medicamentos = [
        'Paracetamol', 'Ibuprofeno', 'Aspirina', 'Amoxicilina', 'Metronidazol',
        'Diclofenaco', 'Naproxeno', 'Ciprofloxacino', 'Azitromicina', 'Doxiciclina',
        'Metformina', 'Omeprazol', 'Ranitidina', 'Loratadina', 'Cetirizina',
        'Ambroxol', 'Salbutamol', 'Prednisona', 'Dexametasona', 'Hidrocortisona',
        'Lidocaina', 'Bupivacaina', 'Fentanilo', 'Morfina', 'Tramadol',
        'Codeina', 'Dipirona', 'Ketorolaco', 'Meloxicam', 'Celecoxib',
        'Enalapril', 'Losartan', 'Amlodipino', 'Nifedipino', 'Propranolol',
        'Atenolol', 'Furosemida', 'Hidroclorotiazida', 'Espironolactona', 'Digoxina',
        'Warfarina', 'Heparina', 'Enoxaparina', 'Clopidogrel', 'Acido Acetilsalicilico',
        'Nitroglicerina', 'Isosorbida', 'Atorvastatina', 'Simvastatina', 'Rosuvastatina',
        'Insulina Glargina', 'Insulina NPH', 'Metformina', 'Glibenclamida', 'Glipizida',
        'Levotiroxina', 'Metimazol', 'Propiltiouracilo', 'Prednisona', 'Hidrocortisona',
        'Fludrocortisona', 'Desmopresina', 'Oxitocina', 'Ergometrina', 'Mifepristona',
        'Misoprostol', 'Dinoprostona', 'Carboprost', 'Magnesio Sulfato', 'Nifedipino',
        'Betametasona', 'Dexametasona Fosfato', 'Fenobarbital', 'Fenitoina', 'Carbamazepina',
        'Valproato de Sodio', 'Levetiracetam', 'Lamotrigina', 'Topiramato', 'Gabapentina',
        'Pregabalina', 'Amitriptilina', 'Imipramina', 'Sertralina', 'Fluoxetina',
        'Paroxetina', 'Escitalopram', 'Venlafaxina', 'Duloxetina', 'Mirtazapina',
        'Risperidona', 'Olanzapina', 'Quetiapina', 'Aripiprazol', 'Haloperidol',
        'Clorpromazina', 'Levodopa', 'Carbidopa', 'Entacapona', 'Pramipexol',
        'Ropinirol', 'Amantadina', 'Donepezilo', 'Galantamina', 'Memantina',
        'Rivastigmina', 'Tacrina', 'Metilfenidato', 'Atomoxetina', 'Clonidina',
        'Guanfacina', 'Lisdexanfetamina', 'Dextroanfetamina', 'Fenilefrina', 'Pseudoefedrina',
        'Oximetazolina', 'Nafazolina', 'Feniramina', 'Bromfeniramina', 'Clorfeniramina',
        'Dimenhidrinato', 'Meclizina', 'Escopolamina', 'Metoclopramida', 'Ondansetron',
        'Granisetron', 'Dolasetron', 'Palonosetron', 'Aprepitant', 'Fosaprepitant',
        'Dexametasona', 'Haloperidol', 'Lorazepam', 'Midazolam', 'Diazepam',
        'Clonazepam', 'Alprazolam', 'Buspirona', 'Hidroxicina', 'Prometazina',
        'Difenhidramina', 'Cinarizina', 'Flunarizina', 'Betahistina', 'Piracetam',
        'Vinpocetina', 'Ginkgo Biloba', 'Coenzima Q10', 'Melatonina', 'Triptofano',
        'Acido Folinico', 'Acido Folico', 'Vitamina B12', 'Vitamina B6', 'Vitamina D',
        'Vitamina C', 'Vitamina E', 'Vitamina A', 'Vitamina K', 'Calcio',
        'Hierro', 'Zinc', 'Magnesio', 'Selenio', 'Yodo',
        'Potasio', 'Sodio', 'Fosforo', 'Cloro', 'Bicarbonato',
        'Glucosa', 'Fructosa', 'Maltodextrina', 'Dextrosa', 'Lactosa',
        'Sorbitol', 'Xilitol', 'Manitol', 'Glicerol', 'Propilenglicol',
        'Alcohol Etílico', 'Alcohol Isopropílico', 'Povidona Yodada', 'Clorhexidina', 'Yodo',
        'Permanganato de Potasio', 'Benzalconio Cloruro', 'Glutaraldehido', 'Formaldehido', 'Hipoclorito de Sodio',
        'Peroxido de Hidrogeno', 'Agua Oxigenada', 'Cloruro de Sodio', 'Cloruro de Potasio', 'Bicarbonato de Sodio',
        'Lactato de Ringer', 'Solucion Salina', 'Solucion Hartmann', 'Plasma Expanders', 'Albúmina',
        'Inmunoglobulina', 'Factor VIII', 'Factor VII', 'Trombina', 'Fibrinogeno',
        'Acido Tranexamico', 'Aminocaproico', 'Protamina', 'Vitamina K1', 'Vitamina K3',
        'Fitonadiona', 'Menadiona', 'Filgrastim', 'Sargramostim', 'Eritropoyetina',
        'Darbepoetina', 'Romiplostim', 'Eltrombopag', 'Oprelvekin', 'Pegfilgrastim',
        'Imatinib', 'Gleevec', 'Dasatinib', 'Nilotinib', 'Bosutinib',
        'Ponatinib', 'Ruxolitinib', 'Tofacitinib', 'Baricitinib', 'Filgotinib',
        'Upadacitinib', 'Abatacept', 'Adalimumab', 'Anakinra', 'Certolizumab',
        'Etanercept', 'Golimumab', 'Infliximab', 'Rituximab', 'Tocilizumab',
        'Sarilumab', 'Secukinumab', 'Ixekizumab', 'Brodalumab', 'Guselkumab',
        'Tildrakizumab', 'Risankizumab', 'Ustekinumab', 'Vedolizumab', 'Natalizumab',
        'Ocrelizumab', 'Ofatumumab', 'Alemtuzumab', 'Bevacizumab', 'Cetuximab',
        'Panitumumab', 'Trastuzumab', 'Pertuzumab', 'Ado-Trastuzumab', 'T-DM1',
        'Rituximab', 'Ibritumomab', 'Tositumomab', 'Brentuximab', 'Gemtuzumab',
        'Inotuzumab', 'Moxetumomab', 'Polatuzumab', 'Enfortumab', 'Sacituzumab',
        'Cisplatino', 'Carboplatino', 'Oxaliplatino', 'Nedaplatino', 'Lobaplatino',
        'Etoposido', 'Teniposido', 'Irinotecan', 'Topotecan', 'Vincristina',
        'Vinblastina', 'Vinorelbina', 'Paclitaxel', 'Docetaxel', 'Cabazitaxel',
        'Abraxane', 'Doxorrubicina', 'Daunorrubicina', 'Epirrubicina', 'Idarrubicina',
        'Mitoxantrona', 'Bleomicina', 'Mitomicina', 'Actinomicina', 'Plicamicina',
        'Metotrexato', 'Pemetrexed', 'Raltitrexed', 'Pralatrexato', 'Cladribina',
        'Clofarabina', 'Citarabina', 'Fludarabina', 'Gemcitabina', 'Azacitidina',
        'Decitabina', '5-Fluorouracilo', 'Capecitabina', 'Tegafur', 'Carmofur',
        'Mercaptopurina', 'Tioguanina', 'Hidroxiurea', 'Procarbazina', 'Dacarbazina',
        'Temozolomida', 'Busulfan', 'Ciclofosfamida', 'Ifosfamida', 'Melphalan',
        'Clorambucilo', 'Bendamustina', 'Treosulfan', 'Carmustina', 'Lomustina',
        'Semustina', 'Fotemustina', 'Estreptozocina', 'Thiotepa', 'Mitotane',
        'Tamoxifeno', 'Toremifeno', 'Raloxifeno', 'Bazedoxifeno', 'Lasofoxifeno',
        'Ospemifeno', 'Clomifeno', 'Fulvestranto', 'Anastrozol', 'Letrozol',
        'Exemestano', 'Formestano', 'Aminoglutetimida', 'Ketoconazol', 'Abiraterona',
        'Enzalutamida', 'Bicalutamida', 'Flutamida', 'Nilutamida', 'Cyproterona',
        'Leuprolide', 'Goserelina', 'Triptorelina', 'Histrelina', 'Degarelix',
        'Cetrorelix', 'Elagolix', 'Relugolix', 'Linagliptina', 'Saxagliptina',
        'Sitagliptina', 'Alogliptina', 'Vildagliptina', 'Empagliflozina', 'Canagliflozina',
        'Dapagliflozina', 'Ertugliflozina', 'Liraglutida', 'Semaglutida', 'Dulaglutida',
        'Exenatida', 'Lixisenatida', 'Tirzepatida', 'Pramlintida', 'Acarbosa',
        'Miglitol', 'Voglibosa', 'Repaglinida', 'Nateglinida', 'Pioglitazona',
        'Rosiglitazona', 'Troglitazona', 'Colesevelam', 'Bromocriptina', 'Canagliflozina'
    ];

    private array $insumos = [
        'Jeringas 1ml', 'Jeringas 3ml', 'Jeringas 5ml', 'Jeringas 10ml', 'Jeringas 20ml',
        'Agujas 21G', 'Agujas 23G', 'Agujas 25G', 'Agujas 18G', 'Agujas 16G',
        'Catéter IV 14G', 'Catéter IV 16G', 'Catéter IV 18G', 'Catéter IV 20G', 'Catéter IV 22G',
        'Catéter IV 24G', 'Catéter Central', 'Catéter PICC', 'Catéter Swan-Ganz', 'Catéter Arterial',
        'Guantes Estériles Talla S', 'Guantes Estériles Talla M', 'Guantes Estériles Talla L', 'Guantes Estériles Talla XL',
        'Guantes de Látex', 'Guantes de Nitrilo', 'Guantes de Vinilo', 'Guantes Quirúrgicos',
        'Mascarilla Quirúrgica', 'Mascarilla N95', 'Mascarilla KN95', 'Mascarilla FFP2', 'Mascarilla FFP3',
        'Bata Quirúrgica', 'Bata de Aislamiento', 'Bata Impermeable', 'Bata Desechable',
        'Gorro Quirúrgico', 'Calzado Quirúrgico', 'Traje de Bioseguridad', 'Overol Protector',
        'Lentes de Protección', 'Careta Facial', 'Escudo Facial', 'Gafas de Seguridad',
        'Tapabocas', 'Cofia', 'Cubrebocas', 'Respirador',
        'Vendas Elásticas', 'Vendas de Gasa', 'Vendas de Yeso', 'Vendas Cohesivas',
        'Gasas Estériles', 'Gasas No Estériles', 'Gasas Oftálmicas', 'Gasas Vaselinadas',
        'Algodón', 'Algodón Zig-Zag', 'Bolas de Algodón', 'Hisopos',
        'Esparadrapo', 'Cinta Micropore', 'Cinta de Seda', 'Cinta de Plástico',
        'Curas Hidrocoloides', 'Curas de Espuma', 'Curas de Alginato', 'Curas de Plata',
        'Curas Transparentes', 'Curas con Gel', 'Apositos Adhesivos', 'Apositos No Adhesivos',
        'Suturas Nylon', 'Suturas Seda', 'Suturas Vicryl', 'Suturas PDS', 'Suturas Monocryl',
        'Suturas Prolene', 'Suturas Chromic', 'Suturas de Acero', 'Staples Quirúrgicos',
        'Hojas de Bisturí No. 10', 'Hojas de Bisturí No. 11', 'Hojas de Bisturí No. 15', 'Hojas de Bisturí No. 20',
        'Bisturí Desechable', 'Tijeras Quirúrgicas', 'Pinzas de Disección', 'Pinzas de Hemostasia',
        'Retractores', 'Separadores', 'Curetas', 'Escofinas',
        'Drenajes de Penrose', 'Drenajes Jackson-Pratt', 'Drenajes Hemovac', 'Drenajes Blake',
        'Sondas Foley 12Fr', 'Sondas Foley 14Fr', 'Sondas Foley 16Fr', 'Sondas Foley 18Fr',
        'Sondas Foley 20Fr', 'Sondas Foley 22Fr', 'Sondas Foley 24Fr',
        'Sondas Nasogástricas 8Fr', 'Sondas Nasogástricas 10Fr', 'Sondas Nasogástricas 12Fr', 'Sondas Nasogástricas 14Fr',
        'Sondas Nasogástricas 16Fr', 'Sondas Nasogástricas 18Fr',
        'Sondas Rectales', 'Sondas Ureterales', 'Sondas de Tórax', 'Sondas de Succión',
        'Bolsa de Colostomía', 'Bolsa de Ileostomía', 'Bolsa de Urostomía', 'Bolsa de Drenaje',
        'Bolsa Orina Adulto', 'Bolsa Orina Pediátrica', 'Bolsa Orina Neonatal',
        'Bolsas de Sangre 250ml', 'Bolsas de Sangre 450ml', 'Bolsas de Plasma',
        'Bolsas de Solución Salina 500ml', 'Bolsas de Solución Salina 1000ml', 'Bolsas de Lactato 1000ml',
        'Bolsas de Dextrosa 5% 1000ml', 'Bolsas de Dextrosa 10% 500ml', 'Bolsas de Dextrosa 50% 50ml',
        'Bolsas de Solución Hartmann 1000ml', 'Bolsas de Plasmalyte', 'Bolsas de Albumina',
        'Sets de Infusión', 'Sets de Transfusión', 'Sets de Microgotas', 'Sets de Macrogotas',
        'Cámaras de Infusión', 'Filtros de Infusión', 'Filtros de Transfusión',
        'Bombas de Infusión', 'Bombas de Jeringa', 'Bombas de Nutrición',
        'Monitores de Signos Vitales', 'Pulsioxímetros', 'Tensiómetros', 'Termómetros',
        'Electrodos ECG', 'Gel para ECG', 'Papel para ECG', 'Tinta para ECG',
        'Desfibriladores', 'Paletas para Desfibrilador', 'Baterías para Desfibrilador',
        'Ventiladores', 'Circuitos de Ventilador', 'Filtros de Ventilador', 'Tubos ETT',
        'Tubos Endotraqueales 6.0', 'Tubos Endotraqueales 6.5', 'Tubos Endotraqueales 7.0',
        'Tubos Endotraqueales 7.5', 'Tubos Endotraqueales 8.0', 'Tubos Endotraqueales 8.5',
        'Máscaras Laringeas', 'Tubos de Laringe', 'Cánulas de Guedel', 'Cánulas de Berman',
        'Máscaras de Venturi', 'Máscaras de Oxígeno Simple', 'Máscaras de Reanimación',
        'Bolsa de Reanimación Adulto', 'Bolsa de Reanimación Pediátrica', 'Bolsa de Reanimación Neonatal',
        'Oxígeno Medicinal', 'Aire Medicinal', 'Óxido Nitroso', 'CO2 Medicinal',
        'Reguladores de Oxígeno', 'Flujómetros', 'Humedecedores', 'Nebulizadores',
        'Cánulas Nasales', 'Gafas Nasales', 'Cánulas de Oxígeno', 'Extensiones de Oxígeno',
        'Tanques de Oxígeno', 'Concentradores de Oxígeno', 'Oxímetros de Pulso',
        'Máquinas de Anestesia', 'Agentes Anestésicos', 'Halotano', 'Isoflurano', 'Sevoflurano',
        'Desflurano', 'Nitroglicerina IV', 'Nitroprusiatos', 'Esmolol', 'Labetalol',
        'Norepinefrina', 'Epinefrina', 'Fenilefrina', 'Vasopresina', 'Terlipresina',
        'Dopamina', 'Dobutamina', 'Milrinona', 'Inamrinona', 'Levosimendan',
        'Amiodarona', 'Lidocaina IV', 'Procaïnamida', 'Atropina', 'Adenosina',
        'Verapamilo', 'Diltiazem', 'Magnesio Sulfato IV', 'Gluconato de Calcio', 'Cloruro de Calcio',
        'Bicarbonato de Sodio', 'Cloruro de Potasio', 'Fosfato de Potasio', 'Acetato de Sodio',
        'Nutrición Parenteral', 'Lipidios Intravenosos', 'Aminoácidos', 'Glucosa IV',
        'Solución Salina 0.9%', 'Solución Salina 0.45%', 'Agua Destilada', 'Agua para Inyección',
        'Jeringas de Insulina', 'Agujas de Insulina', 'Plumas de Insulina', 'Cartuchos de Insulina',
        'Tiras Reactivas Glucosa', 'Lancetas', 'Glucómetros', 'Bombas de Insulina',
        'Sensor de Glucosa', 'Cetonas en Sangre', 'Hemoglobina Glicosilada',
        'Escaneres de Úlceras', 'Materiales para Curación', 'Detergentes Enzimáticos',
        'Desinfectantes de Alto Nivel', 'Esterilizadores', 'Autoclaves', 'Etileno Óxido',
        'Plasma Frío', 'Peróxido de Vapor', 'Luz UV', 'Ozono',
        'Bolsas de Esterilización', 'Indicadores Químicos', 'Indicadores Biológicos', 'Cintas de Esterilización',
        'Envolturas de Esterilización', 'Contenedores de Esterilización', 'Canastillas',
        'Mesa de Operaciones', 'Lámparas Cialíticas', 'Electrobisturí', 'Harmonic Scalpel',
        'Ligasure', 'Bisturí de Plasma', 'Laser Quirúrgico', 'Criocirugía',
        'Laparoscopio', 'Torres de Laparoscopía', 'Instrumentos Laparoscópicos', 'Trocars',
        'Endoscopio', 'Colonoscopio', 'Gastroscopio', 'Broncoscopio', 'Cistoscopio',
        'Histeroscopio', 'Torre de Endoscopía', 'Instrumentos Endoscópicos',
        'Mesa de Partos', 'Fórceps Obstétrico', 'Vacuum Extractor', 'Pinzas de Kocher',
        'Espéculos Vaginales', 'Espéculos Nasales', 'Espéculos Auriculares', 'Espéculos Oftálmicos',
        'Oftalmoscopio', 'Otoscopio', 'Estetoscopio', 'Fonendoscopio', 'Esfigmomanómetro',
        'Martillo de Reflejos', 'Tuning Fork', 'Agujas de Biopsia', 'Agujas de Punción',
        'Agujas de Toracocentesis', 'Agujas de Paracentesis', 'Agujas de Amniocentesis',
        'Agujas de Lumbar', 'Agujas de Espinal', 'Agujas Epidurales', 'Catéter Epidural',
        'Bomba Epidural', 'Anestesia Epidural', 'Kit de Epidural', 'Kit de Espinal',
        'Kit de Aseo Central', 'Kit de Cateterismo', 'Kit de Cirugía Mayor', 'Kit de Cirugía Menor',
        'Kit de Cesárea', 'Kit de Parto', 'Kit de Curación', 'Kit de Sutura',
        'Kit de Biopsia', 'Kit de Punción', 'Kit de Drenaje', 'Kit de Trasfusion',
        'Mesa de Instrumental', 'Charola de Mayo', 'Charola de Instrumental', 'Cuencos de Cirugía',
        'Pinzas de Mayo', 'Pinzas de Metzenbaum', 'Pinzas de Adson', 'Pinzas de Allis',
        'Pinzas de Babcock', 'Pinzas de DeBakey', 'Pinzas de Satinsky', 'Pinzas de Mixter',
        'Tijeras de Metzenbaum', 'Tijeras de Mayo', 'Tijeras de Lister', 'Tijeras de Spencer',
        'Abridor de Caja Torácica', 'Esternotomía', 'Sierra de Sternman', 'Legras de Doyen',
        'Legras de Sims', 'Curetas de Thomas', 'Curetas de Novak', 'Curetas de Kevorkian',
        'Espátulas de Ayre', 'Espátulas de Ayre con Cepillo', 'Espéculo de Graves', 'Espéculo de Pedersen',
        'Tenáculos de Ovum', 'Tenáculos de Pozzi', 'Tenáculos de Jacobi', 'Aplicador de DIU',
        'Removedor de DIU', 'Insertador de DIU', 'DIU de Cobre', 'DIU Hormonal',
        'Implante Subdérmico', 'Anillo Vaginal', 'Diafragma', 'Capuchón Cervical',
        'Condones', 'Lubricantes', 'Gel de Ultrasonido', 'Gel para ECG', 'Gel Conductor',
        'Pasta de Limpieza', 'Solución de Limpieza', 'Toallas Húmedas', 'Paños de Limpieza',
        'Detergente Enzimático', 'Desengrasante', 'Neutralizador', 'Lubricante Quirúrgico',
        'Tinta de Tatuaje Quirúrgico', 'Marcador Quirúrgico', 'Piel Artificial', 'Prótesis',
        'Implantes Mamarios', 'Mallas de Hernia', 'Stents', 'Catéteres de Dialisis',
        'Membranas de Diálisis', 'Líneas de Diálisis', 'Líneas de CRRT', 'Líneas de Plasmapheresis',
        'Líneas de Leucoféresis', 'Líneas de Eritroféresis', 'Líneas de Tromboféresis',
        'Bolsas de Recolección', 'Bolsas de Sangre Autóloga', 'Bolsas de Sangre Iretrograda',
        'Filtros Leucocitarios', 'Irradiadores de Sangre', 'Agitadores de Plaquetas',
        'Neveras de Sangre', 'Termómetros de Sangre', 'Registros de Sangre', 'Etiquetas de Sangre',
        'Sistema de Código de Barras', 'Escáner de Código de Barras', 'Impresora de Etiquetas',
        'Carnets de Paciente', 'Pulseras de Identificación', 'Pulseras de Alerta',
        'Señales de Precaución', 'Señales de Aislamiento', 'Señales de Restricción',
        'Cajas de Seguridad', 'Contenedores de Agujas', 'Contenedores de Riesgo Biológico',
        'Bolsas de Riesgo Biológico', 'Bolsas de Ropa Sucia', 'Bolsas de Lencería',
        'Carros de Paro', 'Carros de Emergencia', 'Carros de Medicamentos', 'Carros de Curación',
        'Carros de Anestesia', 'Carros de Endoscopía', 'Carros de Parto', 'Carros de Neonato',
        'Incubadoras', 'Cunas Térmicas', 'Fototerapia', 'Oxímetros de Pulso Neonatal',
        'Ventiladores Neonatales', 'Circuitos Neonatales', 'Sondas de Succión Neonatal',
        'Máscaras Neonatales', 'Canulas Neonatales', 'Líneas de Infusión Neonatal',
        'Jeringas Neonatales', 'Alimentación Enteral Neonatal', 'Leche Materna', 'Fórmula Láctea',
        'Nutrición Parenteral Neonatal', 'Catéteres Umbilicales', 'Catéteres Periféricos Neonatales',
        'Vías Central Neonatal', 'Trocars Neonatales', 'Agujas Espinal Neonatal',
        'Electrodos Neonatales', 'Filtros Neonatales', 'Calentadores de Líquidos',
        'Calentadores de Sangre', 'Calentadores de Nutrición', 'Calentadores de Soluciones',
        'Sistemas de Aspiración', 'Frascos de Aspiración', 'Tubos de Aspiración', 'Filtros de Aspiración',
        'Reguladores de Succión', 'Manómetros de Succión', 'Baterías de Respuesto', 'Cargadores',
        'Fuente de Poder', 'UPS', 'Reguladores de Voltaje', 'Transformadores',
        'Extensiónes Eléctricas', 'Multicontactos', 'Cables de Poder', 'Cables de Señal',
        'Sensores', 'Transductores', 'Cables de ECG', 'Cables de SpO2', 'Cables de Presión',
        'Mangueras de Aire', 'Mangueras de Oxígeno', 'Mangueras de Vacío', 'Mangueras de Succión',
        'Conectores', 'Adaptadores', 'Enchufes', 'Tomas', 'Valvulas', 'Grifos',
        'Filtros de Aire', 'Filtros de Oxígeno', 'Filtros de Bacterias', 'Filtros de Virus',
        'Filtros HEPA', 'Filtros ULPA', 'Filtros de Carbón', 'Filtros de Agua',
        'Membranas de Filtración', 'Cartuchos de Filtro', 'Sellos', 'Empaques', 'Juntas',
        'Lubricantes de Silicona', 'Grasas Medicinales', 'Aceites Medicinales', 'Vaselina Medicinal',
        'Alcohol en Gel', 'Solución Antiséptica', 'Jabón Quirúrgico', 'Jabón Antiséptico',
        'Clorhexidina Solución', 'Povidona Yodada Solución', 'Yodopovidona', 'Tintura de Yodo',
        'Alcohol 70%', 'Alcohol 90%', 'Formol', 'Glutaraldehido 2%', 'Peróxido de Hidrógeno 3%',
        'Permanganato de Potasio', 'Ácido Peracético', 'Hipoclorito de Sodio 0.5%', 'Hipoclorito de Sodio 1%',
        'Detergente Neutral', 'Detergente Alcalino', 'Detergente Ácido', 'Desincrustante',
        'Secuestrante', 'Ablandador de Agua', 'Neutralizador de pH', 'Indicador de pH',
        'Papel de pH', 'Tiras Reactivas', 'Reactivos de Laboratorio', 'Colorantes',
        'Anticoagulantes', 'Conservantes de Sangre', 'Solución de Almacenamiento', 'Fluidos de Preservación',
        'Formol 10%', 'Formol Bufferado', 'Alcohol 95%', 'Alcohol 100%', 'Xileno',
        'Parafina', 'Crioprotectores', 'Nitrógeno Líquido', 'Hielo Seco', 'Gel Refrigerante',
        'Bolsas Frías', 'Bolsas Calientes', 'Compresas Frías', 'Compresas Calientes',
        'Almohadillas Térmicas', 'Mantas Térmicas', 'Calentadores de Paciente', 'Sistemas de Hipotermia',
        'Sistemas de Normotermia', 'Termómetros de Laboratorio', 'Baños María', 'Hornos de Esterilización',
        'Incubadoras de Laboratorio', 'Agitadores', 'Vortex', 'Centrífugas', 'Microscopios',
        'Lupas', 'Placas de Petri', 'Tubos de Ensayo', 'Pipetas', 'Jeringas de Laboratorio',
        'Probetas', 'Matraces', 'Vasos de Precipitado', 'Embudos', 'Gradillas',
        'Mecheros Bunsen', 'Lupas de Disección', 'Pinzas de Laboratorio', 'Espátulas de Laboratorio',
        'Balanza Analítica', 'Balanza de Precisión', 'Pesas Patrón', 'Masas Calibradas',
        'Guantes de Laboratorio', 'Bata de Laboratorio', 'Gafas de Seguridad', 'Campana de Flujo Laminar',
        'Campana de Extracción', 'Cabina de Seguridad Biológica', 'Cabina de PCR', 'Cabina de Flujo',
        'Autoclave de Laboratorio', 'Esterilizador de Laboratorio', 'Horno de Secado', 'Estufa de Cultivo',
        'Refrigerador de Laboratorio', 'Congelador de Laboratorio', 'Ultra Congelador', 'Nevera de Cadena de Frío',
        'Termógrafo', 'Higrómetro', 'Barómetro', 'Anemómetro', 'Luxómetro',
        'Medidor de CO2', 'Medidor de O2', 'Analizador de Gases', 'Espectrofotómetro',
        'Contador de Células', 'Citómetro de Flujo', 'Microscopio de Fluorescencia', 'Microscopio Electrónico',
        'Centrífuga de Microhematocrito', 'Agitador de Tubos', 'Rotatoria', 'Mezclador Vortex',
        'Pipeteador Automático', 'Dispensador de Reactivos', 'Lector de Microplacas', 'Elisa Reader',
        'Termociclador', 'PCR', 'Electroforesis', 'Secuenciador', 'Espectrómetro de Masas'
    ];

    private array $unidades = ['unidades', 'ml', 'mg', 'gr', 'cm', 'cajas', 'frascos', 'sobres', 'ampollas', 'tabletas', 'capsulas', 'viales', 'bolsas', 'tubos'];

    private array $areas = ['emergencia', 'cirugia', 'hospitalizacion', 'uti', 'usi', 'neonato'];

    public function run(): void
    {
        $totalPorArea = 500;
        $porcentajeMedicamentos = 0.70;

        foreach ($this->areas as $area) {
            echo "Generando {$totalPorArea} registros para área: {$area}\n";

            $cantidadMedicamentos = (int) ($totalPorArea * $porcentajeMedicamentos);
            $cantidadInsumos = $totalPorArea - $cantidadMedicamentos;

            // Generar medicamentos
            for ($i = 0; $i < $cantidadMedicamentos; $i++) {
                $this->crearRegistro($area, 'medicamento');
            }

            // Generar insumos
            for ($i = 0; $i < $cantidadInsumos; $i++) {
                $this->crearRegistro($area, 'insumo');
            }
        }

        echo 'Seeder completado. Total de registros: ' . AlmacenMedicamento::count() . "\n";
    }

    private function crearRegistro(string $area, string $tipo): void
    {
        $nombre = $tipo === 'medicamento'
            ? $this->medicamentos[array_rand($this->medicamentos)]
            : $this->insumos[array_rand($this->insumos)];

        // Agregar sufijo aleatorio para evitar duplicados exactos
        $sufijo = rand(1, 999);
        $nombreCompleto = "{$nombre} {$sufijo}";

        AlmacenMedicamento::create([
            'nombre' => $nombreCompleto,
            'descripcion' => $tipo === 'medicamento'
                ? 'Medicamento de uso hospitalario'
                : 'Insumo médico desechable',
            'area' => $area,
            'precio' => rand(100, 50000) / 100, // Entre 1.00 y 500.00
            'fecha_vencimiento' => Carbon::now()->addDays(rand(180, 1095))->format('Y-m-d'), // 6 meses a 3 años
            'lote' => strtoupper(chr(rand(65, 90)) . chr(rand(65, 90)) . rand(1000, 9999)), // AA1234
            'cantidad' => rand(10, 1000),
            'stock_minimo' => rand(5, 50),
            'unidad_medida' => $this->unidades[array_rand($this->unidades)],
            'tipo' => $tipo,
            'activo' => true,
            'observaciones' => 'Generado automáticamente por seeder',
        ]);
    }
}
