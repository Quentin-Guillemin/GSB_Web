<?php
class PdoGsb
{
	/******************************************************
	* Paramètres
	******************************************************/

	/**
	 * type et nom du serveur de bdd
	 * @var String $serveur
	 */
	private static $serveur = 'mysql:host=localhost';

	/**
	 * nom de la BD 
	 * @var String $bdd
	 */
	private static $bdd = 'dbname=gsb-bdd';

	/**
	 * nom de l'utilisateur utilisé pour la connexion 
	 * @var String $user
	 */   		
	private static $user = 'root'; 

	/**
	 * mdp de l'utilisateur utilisé pour la connexion 
	 * @var String $mdp
	 */  		
	private static $mdp = '';

	/**
	 * objet pdo de la classe Pdo pour la connexion 
	 * @var String $monPdo
	 */ 		
	private static $monPdo;

	/**
	 * utilisé pour savoir si l'objet de la classe Pdo a déjà été créé (ou pas pas créé=null)
	 * @var String $monPdoGsb
	 */ 
	private static $monPdoGsb = null;

	/**
	 * Constructeur privé, crée l'instance de PDO qui sera sollicitée
	 * pour toutes les méthodes de la classe
	 */				
	private function __construct()
	{
		PdoGsb::$monPdo = new PDO(PdoGsb::$serveur.';'.PdoGsb::$bdd, PdoGsb::$user, PdoGsb::$mdp); 
		PdoGsb::$monPdo->query("SET CHARACTER SET utf8");
	}

	/**
    * Destructeur
    */
	public function _destruct(){
		PdoGsb::$monPdo = null;
	}

	/**
	 * Fonction statique qui crée l'unique instance de la classe
	 * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
	 * @return PdoGsb $monPdoGsb l'unique objet de la classe PdoGsb
	 */
	public static function getPdoGsb()
	{
		if(PdoGsb::$monPdoGsb == null)
		{
			PdoGsb::$monPdoGsb = new PdoGsb();
		}
		return PdoGsb::$monPdoGsb;
	}

	/******************************************************
	* Appli
	******************************************************/

	/**
	 * Fonction qui retourne si le login existe et son mot de passe hashé pour un collaborateur
	 * @param String $login
	 * @return array $nb un tableau associatif contenant le résultat de la requète
	 */
	public function getConnexion($login)
	{
		$req = "SELECT COUNT(*) AS 'nb', COL_MDP, COL_MATRICULE  
		FROM collaborateur 
		WHERE COL_LOGIN = ? 
		GROUP BY COL_MATRICULE";
		$res = PdoGsb::$monPdo->prepare($req);
		$res->execute(array($login));
		$nb = $res->fetch();
		return $nb;
	}

	/******************************************************
	* Informations utilisateur
	******************************************************/

	/**
	 * Fonction qui retourne le nom le prénom et le role d'un collaborateur
	 * @param String $COL_MATRICULE
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function getInfo($COL_MATRICULE)
	{
		$req = "SELECT COL_NOM, COL_PRENOM, c.STA_CODE, STA_LIB, COL_DATEEMBAUCHE, c.SEC_CODE, SEC_LIBELLE 
		FROM collaborateur c 
		LEFT JOIN statut s ON c.STA_CODE = s.STA_CODE 
		LEFT JOIN secteur se ON c.SEC_CODE = se.SEC_CODE 
		WHERE COL_MATRICULE = '$COL_MATRICULE' 
		GROUP BY COL_MATRICULE";
		$res = PdoGsb::$monPdo->query($req);
		$res = $res->fetch();
		return $res;
	}

	/**
	 * Fonction qui retourne la region d'un collaborateur
	 * @param String $COL_MATRICULE
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function getRegion($COL_MATRICULE)
	{
		$req = "SELECT r.REG_CODE, REG_NOM  
		FROM region r 
		INNER JOIN travailler t 
		ON r.REG_CODE = t.REG_CODE 
		WHERE COL_MATRICULE = '$COL_MATRICULE'";
		$res = PdoGsb::$monPdo->query($req);
		$res = $res->fetch();
		return $res;
	}

	/**
	 * Fonction qui retourne les coordonnées d'un collaborateur
	 * @param String $COL_MATRICULE
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function getCoordonnees($COL_MATRICULE)
	{
		$req = "SELECT COL_NOM, COL_PRENOM, COL_ADRESSE, COL_CP, COL_VILLE
		FROM collaborateur
		WHERE COL_MATRICULE = '$COL_MATRICULE'";
		$res = PdoGsb::$monPdo->query($req);
		$res = $res->fetch();
		return $res;
	}

	/**
	 * Fonction qui met à jour les coordonnées d'un collaborateur
	 * @param String $COL_MATRICULE
	 * @param String $NOM
	 * @param String $PRENOM
	 * @param String $ADRESSE
	 * @param String $CP
	 * @param String $VILLE
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function updateCoordonnees($COL_MATRICULE, $NOM, $PRENOM, $ADRESSE, $CP, $VILLE)
	{
		$req = "UPDATE collaborateur 
		SET COL_NOM = ?, 
		COL_PRENOM = ?, 
		COL_ADRESSE = ?, 
		COL_CP = ?, 
		COL_VILLE = ? 
		WHERE COL_MATRICULE = ?";
		$res = PdoGsb::$monPdo->prepare($req);
		$res = $res->execute(array($NOM, $PRENOM, $ADRESSE, $CP, $VILLE, $COL_MATRICULE));
		return $res;
	}

	/**
	 * Fonction qui retourne le mot de passe d'un collaborateur
	 * @param String $COL_MATRICULE
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function getPassword($COL_MATRICULE)
	{
		$req = "SELECT COL_MDP 
		FROM collaborateur 
		WHERE COL_MATRICULE = '$COL_MATRICULE'";
		$res = PdoGsb::$monPdo->query($req);
		$res = $res->fetch();
		return $res;
	}

	/**
	 * Fonction qui met à jour le mot de passe d'un collaborateur
	 * @param String $COL_MATRICULE
	 * @param String $NEW_PASS
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function updatePassword($COL_MATRICULE, $NEW_PASS)
	{
		$req = "UPDATE collaborateur 
		SET COL_MDP = ? 
		WHERE COL_MATRICULE = ?";
		$res = PdoGsb::$monPdo->prepare($req);
		$res = $res->execute(array($NEW_PASS, $COL_MATRICULE));
		return $res;
	}
	
	/******************************************************
	* Consulter > Praticiens && Comptes rendus
	******************************************************/

	/**
	 * Fonction qui retourne tout les praticiens id et nom
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function getPraticiens()
	{
		$req = "SELECT PRA_NUM, PRA_NOM, PRA_PRENOM 
		FROM praticien 
		ORDER BY PRA_NOM";
		$res = PdoGsb::$monPdo->query($req);
		$res = $res->fetchAll();
		return $res;
	}

	/******************************************************
	* Consulter > Praticiens
	******************************************************/

	/**
	 * Fonction qui retourne les infos d'un praticien
	 * @param int $PRA_NUM
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function getInfosPraticien($PRA_NUM)
	{
		$req = "SELECT * 
		FROM praticien p 
		INNER JOIN type_praticien tp 
		ON p.TYP_CODE = tp.TYP_CODE 
		WHERE PRA_NUM = '$PRA_NUM'";
		$res = PdoGsb::$monPdo->query($req);
		$res = $res->fetch();
		return $res;
	}

	/**
	 * Fonction qui retourne les spécialités d'un praticien
	 * @param int $PRA_NUM
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function getSpecialites($PRA_NUM)
	{
		$req = "SELECT SPE_LIBELLE 
		FROM posseder p 
		INNER JOIN specialite s 
		ON p.SPE_CODE = s.SPE_CODE 
		WHERE PRA_NUM = '$PRA_NUM'";
		$res = PdoGsb::$monPdo->query($req);
		$res = $res->fetchAll();
		return $res;
	}

	/******************************************************
	* Consulter > Medicaments && Comptes rendus
	******************************************************/

	/**
	 * Fonction qui retourne tout les médicaments id et nom
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function getMedicaments()
	{
		$req = "SELECT MED_DEPOTLEGAL, MED_NOMCOMMERCIAL 
		FROM medicament 
		ORDER BY MED_NOMCOMMERCIAL";
		$res = PdoGsb::$monPdo->query($req);
		$res = $res->fetchAll();
		return $res;
	}

	/******************************************************
	* Consulter > Medicaments
	******************************************************/

	/**
	 * Fonction qui retourne les infos d'un medicament
	 * @param String $MED_DEPOTLEGAL
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function getInfosMedicament($MED_DEPOTLEGAL)
	{
		$req = "SELECT * 
		FROM medicament m 
		INNER JOIN famille f 
		ON m.FAM_CODE = f.FAM_CODE 
		WHERE MED_DEPOTLEGAL = '$MED_DEPOTLEGAL'";
		$res = PdoGsb::$monPdo->query($req);
		$res = $res->fetch();
		return $res;
	}

	/**
	 * Fonction qui retourne les composants d'un medicament
	 * @param String $MED_DEPOTLEGAL
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function getCompositionMedicament($MED_DEPOTLEGAL)
	{
		$req = "SELECT CMP_LIBELLE, CST_QTE 
		FROM constituer con
		INNER JOIN composant com
		ON com.CMP_CODE = con.CMP_CODE
		WHERE con.MED_DEPOTLEGAL = '$MED_DEPOTLEGAL'";
		$res = PdoGsb::$monPdo->query($req);
		$res = $res->fetchAll();
		return $res;
	}

	/**
	 * Fonction qui retourne les medicaments interagissant avec un medicament
	 * @param String $MED_DEPOTLEGAL
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function getReactionsMedicament($MED_DEPOTLEGAL)
	{
		$req = "SELECT MED_PERTURBATEUR
		FROM interagir
		WHERE MED_PERTURBE = '$MED_DEPOTLEGAL'";
		$res = PdoGsb::$monPdo->query($req);
		$res = $res->fetchAll();
		return $res;
	}

	/******************************************************
	* Comptes rendus > Consulter
	******************************************************/

	/**
	 * Fonction qui retourne les rapports non validés d'un collaborateur
	 * 
	 * @param String $COL_MATRICULE
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function getRapportsNonValides($COL_MATRICULE)
	{
		$req = "SELECT RAP_NUM, RAP_DATE, RAP_DATEVISITE, rv.PRA_NUM, PRA_NOM 
		FROM rapport_visite rv 
		INNER JOIN praticien p 
		ON rv.PRA_NUM = p.PRA_NUM 
		WHERE COL_MATRICULE = '$COL_MATRICULE' AND RAP_DEF = 0";
		$res = PdoGsb::$monPdo->query($req);
		$res = $res->fetchAll();
		return $res;
	}

	/******************************************************
	* Comptes Rendu > Consulter > Recherche
	******************************************************/

	/**
	 * Fonction qui retourne les praticiens ayant un rapport définitif ecrit par un collaborateur
	 * @param String $COL_MATRICULE
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function getPraticiensVu($COL_MATRICULE)
	{
		$req = "SELECT DISTINCT PRA_NOM, rv.PRA_NUM 
		FROM rapport_visite rv 
		INNER JOIN praticien p 
		ON rv.PRA_NUM = p.PRA_NUM 
		WHERE COL_MATRICULE = '$COL_MATRICULE' AND RAP_DEF = 1 
		ORDER BY PRA_NOM";
		$res = PdoGsb::$monPdo->query($req);
		$res = $res->fetchAll();
		return $res;
	}

	/**
	 * Fonction qui retourne les rapports d'un collaborateur sur une periode (pour un praticien /facultatif/)
	 * @param String $COL_MATRICULE
	 * @param date $RAP_DATE1
	 * @param date $RAP_DATE2
	 * @param int $PRA_NUM
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function getRapports($COL_MATRICULE, $RAP_DATE1, $RAP_DATE2, $PRA_NUM)
	{
		if (!empty($PRA_NUM))
		{
			$req = "SELECT RAP_NUM, RAP_DATE, RAP_DATEVISITE, rv.PRA_NUM, PRA_NOM 
			FROM rapport_visite rv 
			INNER JOIN praticien p 
			ON rv.PRA_NUM = p.PRA_NUM 
			WHERE RAP_DATEVISITE BETWEEN '$RAP_DATE1' AND '$RAP_DATE2' AND p.PRA_NUM = $PRA_NUM AND COL_MATRICULE = '$COL_MATRICULE' AND RAP_DEF = 1 
			ORDER BY RAP_DATEVISITE DESC";
		}
		else
		{
			$req = "SELECT RAP_NUM, RAP_DATE, RAP_DATEVISITE, rv.PRA_NUM, PRA_NOM 
			FROM rapport_visite rv 
			INNER JOIN praticien p 
			ON rv.PRA_NUM = p.PRA_NUM 
			WHERE RAP_DATEVISITE BETWEEN '$RAP_DATE1' AND '$RAP_DATE2' AND COL_MATRICULE = '$COL_MATRICULE' AND RAP_DEF = 1 
			ORDER BY RAP_DATEVISITE DESC";
		}
		$res = PdoGsb::$monPdo->query($req);
		$res = $res->fetchAll();
		return $res;
	}

	/******************************************************
	* Comptes rendus > Région
	******************************************************/

	/**
	 * Fonction qui retourne les nouveaux rapports de la région
	 * @param String $REG_NOM
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function getNouveauxRapportsRegion($REG_CODE)
	{
		$req = "SELECT RAP_NUM, RAP_DATE, RAP_DATEVISITE, rv.PRA_NUM, PRA_NOM 
		FROM rapport_visite rv 
		INNER JOIN praticien p 
		ON rv.PRA_NUM = p.PRA_NUM 
		INNER JOIN collaborateur c
		ON c.COL_MATRICULE = rv.COL_MATRICULE
		INNER JOIN travailler t
		ON t.COL_MATRICULE = c.COL_MATRICULE
		INNER JOIN region r 
		ON r.REG_CODE = t.REG_CODE
		WHERE  r.REG_CODE ='$REG_CODE' AND RAP_DEF = 1 AND RAP_CONSULTE = 0";
		$res = PdoGsb::$monPdo->query($req);
		$res = $res->fetchAll();
		return $res;
	}

	/**
	 * Fonction qui met à jour le champ de consultation par un délégué
	 * @param String $NUM_RAP
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function rapportConsulteDelegue($NUM_RAP)
	{
		$req = "UPDATE rapport_visite
		SET RAP_CONSULTE = 1
		WHERE RAP_NUM = ?";
		$res = PdoGsb::$monPdo->prepare($req);
		$res = $res->execute(array($NUM_RAP));
		return $res;
	}

	/******************************************************
	* Comptes Rendu > Region > Recherche
	******************************************************/

	/**
	 * Retourne les praticiens appartenant à la région ayant des rapports définitifs consultés par un délégué
	 * @param String $REG_CODE
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function getPraticiensRapDefRegion($REG_CODE)
	{
		$req = "SELECT DISTINCT p.PRA_NUM, PRA_NOM, PRA_PRENOM 
		FROM praticien p 
		INNER JOIN rapport_visite rv 
		ON rv.PRA_NUM = p.PRA_NUM 
		INNER JOIN collaborateur c 
		ON c.COL_MATRICULE = rv.COL_MATRICULE 
		INNER JOIN travailler t 
		ON t.COL_MATRICULE = c.COL_MATRICULE 
		INNER JOIN region r 
		ON r.REG_CODE = t.REG_CODE 
		WHERE r.REG_CODE = '$REG_CODE' AND RAP_DEF = 1 AND RAP_CONSULTE = 1 
		ORDER BY PRA_NOM";
		$res = PdoGsb::$monPdo->query($req);
		$res = $res->fetchAll();
		return $res;
	}

	/**
	 * Retourne les rapports définitifs d'une région entre deux dates (pour un praticien) ayant été consulté par un délégué
	 * @param String $REG_CODE
	 * @param date $RAP_DATE1
	 * @param date $RAP_DATE2
	 * @param String $PRA_NUM
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function getRapportsDefRegion($REG_CODE, $RAP_DATE1, $RAP_DATE2, $PRA_NUM)
	{
		if (!empty($PRA_NUM))
		{
			$req = "SELECT RAP_NUM, RAP_DATE, RAP_DATEVISITE, rv.PRA_NUM, PRA_NOM 
			FROM rapport_visite rv 
			INNER JOIN praticien p 
			ON rv.PRA_NUM = p.PRA_NUM 
			INNER JOIN collaborateur c 
			ON c.COL_MATRICULE = rv.COL_MATRICULE 
			INNER JOIN travailler t 
			ON t.COL_MATRICULE = c.COL_MATRICULE 
			INNER JOIN region r 
			ON r.REG_CODE = t.REG_CODE 
			WHERE r.REG_CODE = '$REG_CODE' AND RAP_DEF = 1 AND RAP_CONSULTE = 1 AND RAP_DATEVISITE BETWEEN '$RAP_DATE1' AND '$RAP_DATE2' AND p.PRA_NUM = '$PRA_NUM' 
			ORDER BY RAP_DATEVISITE DESC";
		}
		else
		{
			$req = "SELECT RAP_NUM, RAP_DATE, RAP_DATEVISITE, rv.PRA_NUM, PRA_NOM 
			FROM rapport_visite rv 
			INNER JOIN praticien p 
			ON rv.PRA_NUM = p.PRA_NUM 
			INNER JOIN collaborateur c 
			ON c.COL_MATRICULE = rv.COL_MATRICULE 
			INNER JOIN travailler t 
			ON t.COL_MATRICULE = c.COL_MATRICULE 
			INNER JOIN region r 
			ON r.REG_CODE = t.REG_CODE 
			WHERE r.REG_CODE ='$REG_CODE' AND RAP_DEF = 1 AND RAP_CONSULTE = 1 AND RAP_DATEVISITE BETWEEN '$RAP_DATE1' AND '$RAP_DATE2' 
			ORDER BY RAP_DATEVISITE DESC";
		}
		$res = PdoGsb::$monPdo->query($req);
		$res = $res->fetchAll();
		return $res;
	}

	/******************************************************
	* Comptes Rendu
	******************************************************/

	/**
	 * Fonction qui retourne les motifs de rapport
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function getMotifs()
	{
		$req = "SELECT * 
		FROM motif 
		ORDER BY MOT_LIB";
		$res = PdoGsb::$monPdo->query($req);
		$res = $res->fetchAll();
		return $res;
	}

	/******************************************************
	* Comptes Rendu > Nouveau
	******************************************************/

	/**
	 * Retourne le dernier numéro de rapport du collaborateur
	 * @param String $COL_MATRICULE
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function getNumRapport($COL_MATRICULE)
	{
		$req = "SELECT max(RAP_NUM) 
		FROM rapport_visite 
		WHERE COL_MATRICULE = '$COL_MATRICULE'";
		$res = PdoGsb::$monPdo->query($req);
		$res = $res->fetch();
		return $res;
	}

	/**
	 * Fonction qui insère un rapport
	 * @param varchar $COL_MATRICULE
	 * @param int $RAP_NUM
	 * @param int $PRA_NUM
	 * @param int $PRA_NUM_REMPLACANT
	 * @param date $RAP_DATE
	 * @param String $RAP_BILAN
	 * @param char(3) $MOT_CODE
	 * @param String $MOT_AUTRE
	 * @param int $MED_PRESENTE1
	 * @param int $MED_PRESENTE2
	 * @param boolean $RAP_DEF
	 * @param date $RAP_DATEVISITE
	 * @return boolean $res contenant le résultat de la requète
	 */
	public function nouveauRapport($COL_MATRICULE, $RAP_NUM, $PRA_NUM, $PRA_NUM_REMPLACANT, $RAP_DATE, $RAP_BILAN, $MOT_CODE, $MOT_AUTRE, $MED_PRESENTE1, $MED_PRESENTE2, $RAP_DEF, $RAP_DATEVISITE)
	{
		if (empty($RAP_DATE))
			$RAP_DATE = null;

		if (empty($MOT_AUTRE))
			$MOT_AUTRE = null;

		if (empty($MED_PRESENTE1))
			$MED_PRESENTE1 = null;

		if (empty($MED_PRESENTE2))
			$MED_PRESENTE2 = null;

		if (empty($PRA_NUM_REMPLACANT))
			$PRA_NUM_REMPLACANT = null;

		$req = "INSERT INTO rapport_visite VALUES 
		(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
		$res = PdoGsb::$monPdo->prepare($req);
		$res = $res->execute(array($COL_MATRICULE, $RAP_NUM, $PRA_NUM, $PRA_NUM_REMPLACANT, $RAP_DATE, $RAP_BILAN, $MOT_CODE, $MOT_AUTRE, $MED_PRESENTE1, $MED_PRESENTE2, $RAP_DEF, $RAP_DATEVISITE));
		return $res;
	}

	/**
	 * Fonction qui insère un produit offert
	 * @param String $COL_MATRICULE
	 * @param int $RAP_NUM
	 * @param String $MED_DEPOTLEGAL
	 * @param int $OFF_QTE
	 * @return boolean $res contenant le résultat de la requète
	 */
	public function offrir($COL_MATRICULE, $RAP_NUM, $MED_DEPOTLEGAL, $OFF_QTE)
	{
		$req = "INSERT INTO offrir VALUES 
		(?, ?, ?, ?)";
		$res = PdoGsb::$monPdo->prepare($req);
		$res = $res->execute(array($COL_MATRICULE, $RAP_NUM, $MED_DEPOTLEGAL, $OFF_QTE));
		return $res;
	}

	/******************************************************
	* Comptes Rendu > Modifier && Consulter > * >Recherche > Détail
	******************************************************/

	/**
	 * Fonction qui retourne le détail d'un rapport
	 * @param int $RAP_NUM
	 * @return array $res un tableau associatif contenant le résultat de la requète
	 */
	public function getDetailsRapport($RAP_NUM)
	{
		$req = "SELECT rv.*, MOT_LIB, p.PRA_NOM, p2.PRA_NOM AS 'PRA_NOM_REMPLACANT' 
		FROM rapport_visite rv 
		INNER JOIN praticien p 
		ON rv.PRA_NUM = p.PRA_NUM 
		LEFT JOIN praticien p2 
		ON rv.PRA_NUM_REMPLACANT = p2.PRA_NUM 
		INNER JOIN motif m 
		ON rv.MOT_CODE = m.MOT_CODE 
		WHERE rv.RAP_NUM = $RAP_NUM";
		$res = PdoGsb::$monPdo->query($req);
		$res = $res->fetch();
		return $res;
	}

	/**
	 * Fonction qui retourne tout les echantillons d'un rapport
	 * @param String
	 * @param int
	 * @return boolean $res contenant le résultat de la requète
	 */
	public function getEchantillons($COL_MATRICULE, $RAP_NUM)
	{
		$req = "SELECT MED_DEPOTLEGAL, OFF_QTE 
		FROM offrir 
		WHERE COL_MATRICULE = '$COL_MATRICULE' AND RAP_NUM = $RAP_NUM";
		$res = PdoGsb::$monPdo->query($req);
		$res = $res->fetchAll();
		return $res;
	}

	/******************************************************
	* Comptes Rendu > Modifier
	******************************************************/

	/**
	 * Fonction qui met a jour un rapport
	 * @param int $RAP_NUM_OLD
	 * @param int $RAP_NUM
	 * @param int $PRA_NUM
	 * @param int $PRA_NUM_REMPLACANT
	 * @param date $RAP_DATE
	 * @param String $RAP_BILAN
	 * @param char(3) $MOT_CODE
	 * @param String $MOT_AUTRE
	 * @param int $MED_PRESENTE1
	 * @param int $MED_PRESENTE2
	 * @param boolean $RAP_DEF
	 * @param date $RAP_DATEVISITE
	 * @return boolean $res contenant le résultat de la requète
	 */
	public function updateRapport($RAP_NUM_OLD, $PRA_NUM, $PRA_NUM_REMPLACANT, $RAP_DATE, $RAP_BILAN, $MOT_CODE, $MOT_AUTRE, $MED_PRESENTE1, $MED_PRESENTE2, $RAP_DEF, $RAP_DATEVISITE)
	{
		if (empty($RAP_DATE))
			$RAP_DATE = null;
		
		if (empty($MOT_AUTRE))
			$MOT_AUTRE = null;

		if (empty($MED_PRESENTE1))
			$MED_PRESENTE1 = null;

		if (empty($MED_PRESENTE2))
			$MED_PRESENTE2 = null;

		if (empty($PRA_NUM_REMPLACANT))
			$PRA_NUM_REMPLACANT = null;

		$req = "UPDATE rapport_visite SET 
		PRA_NUM = ?, 
		PRA_NUM_REMPLACANT = ?, 
		RAP_DATE = ?, 
		RAP_BILAN = ?, 
		MOT_CODE = ?, 
		MOT_AUTRE = ?, 
		MED_PRESENTE1 = ?, 
		MED_PRESENTE2 = ?, 
		RAP_DEF = ?, 
		RAP_DATEVISITE = ? 
		WHERE RAP_NUM = $RAP_NUM_OLD";
		$res = PdoGsb::$monPdo->prepare($req);
		$res = $res->execute(array($PRA_NUM, $PRA_NUM_REMPLACANT, $RAP_DATE, $RAP_BILAN, $MOT_CODE, $MOT_AUTRE, $MED_PRESENTE1, $MED_PRESENTE2, $RAP_DEF, $RAP_DATEVISITE));
		return $res;
	}

	/**
	 * Fonction qui supprime tout les echantillons d'un rapport
	 * @param String $COL_MATRICULE
	 * @param int RAP_NUM
	 * @return boolean $res contenant le résultat de la requète
	 */
	public function deleteEchantillons($COL_MATRICULE, $RAP_NUM)
	{
		$req = "DELETE 
		FROM offrir
		WHERE COL_MATRICULE = ? AND RAP_NUM = ?";
		$res = PdoGsb::$monPdo->prepare($req);
		$res = $res->execute(array($COL_MATRICULE, $RAP_NUM));
		return $res;
	}

	/**
	 * Fonction qui met a jour le coefficien de confiance d'un praticien
	 * @param int $PRA_NUM
	 * @param int $PRA_COEFCONFIANCE
	 * @return boolean $res contenant le résultat de la requète
	 */
	public function updateCoefConfiance($PRA_NUM, $PRA_COEFCONFIANCE)
	{
		$req = "UPDATE praticien 
		SET PRA_COEFCONFIANCE = ? 
		WHERE PRA_NUM = ?";
		$res = PdoGsb::$monPdo->prepare($req);
		$res = $res->execute(array($PRA_NUM, $PRA_COEFCONFIANCE));
		return $res;
	}

	/**
	 * Supprime un rapport
	 * @param String $COL_MATRICULE
	 * @param int $RAP_NUM
	 * @return boolean $res contenant le résultat de la requète
	 */
	public function deleteRapport($COL_MATRICULE, $RAP_NUM)
	{
		$req = "DELETE 
		FROM rapport_visite 
		WHERE COL_MATRICULE = ? AND RAP_NUM = ?";
		$res = PdoGsb::$monPdo->prepare($req);
		$res = $res->execute(array($COL_MATRICULE, $RAP_NUM));			
		return $res;
	}
}
?>