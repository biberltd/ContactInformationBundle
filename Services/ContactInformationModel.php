<?php
/**
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        14.12.2015
 */

namespace BiberLtd\Bundle\ContactInformationBundle\Services;

/** Entities to be used */
use BiberLtd\Bundle\ContactInformationBundle\Entity as BundleEntity;
use BiberLtd\Bundle\LogBundle\Entity as LBEntity;
use BiberLtd\Bundle\MemberManagementBundle\Entity as MMBEntity;
/** Models to be loaded */
use BiberLtd\Bundle\LogBundle\Services as LBService;
use BiberLtd\Bundle\MemberManagementBundle\Services as MMBService;
/** Core Service */
use BiberLtd\Bundle\CoreBundle\CoreModel;
use BiberLtd\Bundle\CoreBundle\Services as CoreServices;
use BiberLtd\Bundle\CoreBundle\Exceptions as CoreExceptions;

class ContactInformationModel extends CoreModel {

    /**
     * ContactInformationModel constructor.
     *
     * @param object $kernel
     * @param string $db_connection
     * @param string $orm
     */
    public function __construct($kernel, $db_connection = 'default', $orm = 'doctrine') {
        parent::__construct($kernel, $db_connection, $orm);
        /**
         * Register entity names for easy reference.
         */
        $this->entity = array(
            'cit' => array('name' => 'ContactInformationBundle:ContactInformationType', 'alias' => 'cit'),
            'citl' => array('name' => 'ContactInformationBundle:ContactInformationTypeLocalization', 'alias' => 'citl'),
            'ea' => array('name' => 'ContactInformationBundle:EmailAddress', 'alias' => 'ea'),
            'eaom' => array('name' => 'ContactInformationBundle:EmailAddressesOfMember', 'alias' => 'eaom'),
            'pn' => array('name' => 'ContactInformationBundle:PhoneNumber', 'alias' => 'pn'),
            'pnom' => array('name' => 'ContactInformationBundle:PhoneNumbersOfMember', 'alias' => 'pnom'),
            'sa' => array('name' => 'ContactInformationBundle:SocialAccount', 'alias' => 'sc'),
        );
    }

    /**
     * Destructor
     */
    public function __destruct() {
        foreach ($this as $property => $value) {
            $this->$property = null;
        }
    }

    /**
     * @param $item
     *
     * @return array
     */
    public function deleteEmailAddress($item) {
        return $this->deleteEmailAddresses(array($item));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\ContactInformationBundle\Services\ModelResponse|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deleteEmailAddresses(array $collection){
        $timeStamp = time();
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countDeleted = 0;
        foreach($collection as $entry){
            if($entry instanceof BundleEntity\EmailAddress){
                $this->em->remove($entry);
                $countDeleted++;
            }
            else{
                $response = $this->getEmailAddress($entry);
                if(!$response->error->exist){
                    $this->em->remove($response->result->set);
                    $countDeleted++;
                }
            }
        }
        if($countDeleted < 0){
            return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, time());
        }
        $this->em->flush();

        return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, time());
    }

    /**
     * @param $item
     *
     * @return array
     */
    public function deleteContactInformationType($item) {
        return $this->deleteContactInformationTypes(array($item));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\ContactInformationBundle\Services\ModelResponse|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deleteContactInformationTypes(array $collection){
        $timeStamp = time();
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countDeleted = 0;
        foreach($collection as $entry){
            if($entry instanceof BundleEntity\ContactInformationType){
                $this->em->remove($entry);
                $countDeleted++;
            }
            else{
                $response = $this->getContactInformationType($entry);
                if(!$response->error->exist){
                    $this->em->remove($response->result->set);
                    $countDeleted++;
                }
            }
        }
        if($countDeleted < 0){
            return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, time());
        }
        $this->em->flush();

        return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, time());
    }

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function deletePhoneNumber($item) {
        return $this->deletePhoneNumbers(array($item));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\ContactInformationBundle\Services\ModelResponse|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deletePhoneNumbers(array $collection){
        $timeStamp = time();
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countDeleted = 0;
        foreach($collection as $entry){
            if($entry instanceof BundleEntity\PhoneNumber){
                $this->em->remove($entry);
                $countDeleted++;
            }
            else{
                $response = $this->getPhoneNumber($entry);
                if(!$response->error->exist){
                    $this->em->remove($response->result->set);
                    $countDeleted++;
                }
            }
        }
        if($countDeleted < 0){
            return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, time());
        }
        $this->em->flush();

        return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, time());
    }

    /**
     * @param mixed $type
     * @param bool $bypass
     *
     * @return bool
     */
    public function doesContactInformationTypeExist($type, \bool $bypass = false)
    {
        $response = $this->getContactInformationType($type);
        $exist = true;
        if ($response->error->exist) {
            $exist = false;
            $response->result->set = false;
        }
        if ($bypass) {
            return $exist;
        }
        return $response;
    }

    /**
     * @param mixed $email
     * @param bool $bypass
     *
     * @return bool|mixed
     */
    public function doesEmailAddressExist($email, \bool $bypass = false)
    {
        $response = $this->getEmailAddress($email);
        $exist = true;
        if ($response->error->exist) {
            $exist = false;
            $response->result->set = false;
        }
        if ($bypass) {
            return $exist;
        }
        return $response;
    }

    /**
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listEmailAddresses(array $filter = null, array $sortOrder = null, array $limit = null){
        $timeStamp = time();
        if(!is_array($sortOrder) && !is_null($sortOrder)){
            return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
        }
        $oStr = $wStr = $gStr = $fStr = '';

        $qStr = 'SELECT '.$this->entity['ea']['alias']
            .' FROM '.$this->entity['ea']['name'].' '.$this->entity['ea']['alias'];

        if(!is_null($sortOrder)){
            foreach($sortOrder as $column => $direction){
                switch($column){
                    case 'id':
                    case 'email':
                    case 'date_added':
                    case 'date_updated':
                    case 'date_removed':
                        $column = $this->entity['ea']['alias'].'.'.$column;
                        break;
                }
                $oStr .= ' '.$column.' '.strtoupper($direction).', ';
            }
            $oStr = rtrim($oStr, ', ');
            $oStr = ' ORDER BY '.$oStr.' ';
        }

        if(!is_null($filter)){
            $fStr = $this->prepareWhere($filter);
            $wStr .= ' WHERE '.$fStr;
        }

        $qStr .= $wStr.$gStr.$oStr;
        $q = $this->em->createQuery($qStr);
        $q = $this->addLimit($q, $limit);

        $result = $q->getResult();

        $totalRows = count($result);
        if ($totalRows < 1) {
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
        }
        return new ModelResponse($result, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }

    /**
     * @param mixed $email
     *
     * @return \BiberLtd\Bundle\ContactInformationBundle\Services\ModelResponse
     */
    public function getEmailAddress($email)
    {
        $timeStamp = time();
        if ($email instanceof BundleEntity\EmailAddress) {
            return new ModelResponse($email, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
        }
        $result = null;
        switch ($email) {
            case is_numeric($email):
                $result = $this->em->getRepository($this->entity['ea']['name'])->findOneBy(array('id' => $email));
                break;
            case is_string($email):
                $result = $this->em->getRepository($this->entity['ea']['name'])->findOneBy(array('email' => $email));
                break;
        }
        if (is_null($result)) {
            return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, time());
        }

        return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }

    /**
     * @param mixed $email
     *
     * @return array
     */
    public function insertEmailAddress($email) {
        return $this->insertEmailAddresses(array($email));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\ContactInformationBundle\Services\ModelResponse|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function insertEmailAddresses(array $collection)
    {
        $timeStamp = time();
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countInserts = 0;
        $insertedItems = array();
        $now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\EmailAddress) {
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else if (is_object($data)) {
                $entity = new BundleEntity\EmailAddress();
                if (!property_exists($data, 'date_added')) {
                    $data->date_added = $now;
                }
                if (!property_exists($data, 'date_updated')) {
                    $data->date_updated = $now;
                }
                foreach ($data as $column => $value) {
                    $localeSet = false;
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        default:
                            $entity->$set($value);
                            break;
                    }
                }
                $this->em->persist($entity);
                $insertedItems[] = $entity;

                $countInserts++;
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
    }

    /**
     * @param $email
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function updateEmailAddress($email) {
        return $this->updateEmailAddresses(array($email));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\ContactInformationBundle\Services\ModelResponse
     */
    public function updateEmailAddresses(array $collection)
    {
        $timeStamp = time();
        $countUpdates = 0;
        $updatedItems = array();
        $now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\EmailAddress) {
                $entity = $data;
                $this->em->persist($entity);
                $updatedItems[] = $entity;
                $countUpdates++;
            } else if (is_object($data)) {
                if (!property_exists($data, 'id') || !is_numeric($data->id)) {
                    return $this->createException('InvalidParameter', 'Each data must contain a valid identifier id, integer', 'err.invalid.parameter.collection');
                }
                if (!property_exists($data, 'date_updated')) {
                    $data->date_updated = $now;
                }
                if (property_exists($data, 'date_added')) {
                    unset($data->date_added);
                }
                $response = $this->getEmailAddress($data->id);
                if ($response->error->exist) {
                    return $this->createException('EntityDoesNotExist', 'Email address with id ' . $data->id, 'err.invalid.entity');
                }
                $oldEntity = $response->result->set;
                foreach ($data as $column => $value) {
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'id':
                            break;
                        default:
                            $oldEntity->$set($value);
                            break;
                    }
                    if ($oldEntity->isModified()) {
                        $this->em->persist($oldEntity);
                        $countUpdates++;
                        $updatedItems[] = $oldEntity;
                    }
                }
            }
        }
        if ($countUpdates > 0) {
            $this->em->flush();
            return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, time());
    }

    /**
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listContactInformaionTypes(array $filter = null, array $sortOrder = null, array $limit = null){
        $timeStamp = time();
        if(!is_array($sortOrder) && !is_null($sortOrder)){
            return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
        }
        $oStr = $wStr = $gStr = $fStr = '';

        $qStr = 'SELECT '.$this->entity['cit']['alias'].', '.$this->entity['citl']['alias']
            .' FROM '.$this->entity['citl']['name'].' '.$this->entity['citl']['alias']
            .' JOIN '.$this->entity['citl']['alias'].'.type '.$this->entity['citl']['alias'];

        if(!is_null($sortOrder)){
            foreach($sortOrder as $column => $direction){
                switch($column){
                    case 'id':
                    case 'code':
                    case 'date_added':
                    case 'date_updated':
                    case 'date_removed':
                        $column = $this->entity['cit']['alias'].'.'.$column;
                        break;
                    case 'name':
                    case 'url_key':
                        $column = $this->entity['citl']['alias'].'.'.$column;
                        break;
                }
                $oStr .= ' '.$column.' '.strtoupper($direction).', ';
            }
            $oStr = rtrim($oStr, ', ');
            $oStr = ' ORDER BY '.$oStr.' ';
        }

        if(!is_null($filter)){
            $fStr = $this->prepareWhere($filter);
            $wStr .= ' WHERE '.$fStr;
        }

        $qStr .= $wStr.$gStr.$oStr;
        $q = $this->em->createQuery($qStr);
        $q = $this->addLimit($q, $limit);

        $result = $q->getResult();

        $entities = array();
        foreach($result as $entry){
            $id = $entry->getCategory()->getId();
            if(!isset($unique[$id])){
                $entities[] = $entry->getCategory();
                $unique[$id] = '';
            }
        }
        $totalRows = count($entities);
        if ($totalRows < 1) {
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, time());
        }
        return new ModelResponse($entities, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }

    /**
     * @param mixed $type
     *
     * @return \BiberLtd\Bundle\ContactInformationBundle\Services\ModelResponse
     */
    public function getContactInformationType($type)
    {
        $timeStamp = time();
        if ($type instanceof BundleEntity\ContactInformationType) {
            return new ModelResponse($type , 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
        }
        $result = null;
        switch ($type) {
            case is_numeric($type):
                $result = $this->em->getRepository($this->entity['cit']['name'])->findOneBy(array('id' => $type));
                break;
            case is_string($type):
                $result = $this->em->getRepository($this->entity['cit']['name'])->findOneBy(array('code' => $type));
                if (is_null($result)) {
                    $response = $this->getContactInformationTypeByUrlKey($type);
                    if (!$response->error->exist) {
                        $result = $response->result->set;
                    }
                }
                unset($response);
                break;
        }
        if (is_null($result)) {
            return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, time());
        }

        return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
    }

    /**
     * @param string $urlKey
     * @param mixed|null   $language
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getContactInformationTypeByUrlKey(\string $urlKey, $language = null)
    {
        $timeStamp = time();
        if (!is_string($urlKey)) {
            return $this->createException('InvalidParameterValueException', '$urlKey must be a string.', 'E:S:007');
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['citl']['alias'] . '.url_key', 'comparison' => '=', 'value' => $urlKey),
                )
            )
        );
        if (!is_null($language)) {
            $mModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
            $response = $mModel->getLanguage($language);
            if (!$response->error->exist) {
                $filter[] = array(
                    'glue' => 'and',
                    'condition' => array(
                        array(
                            'glue' => 'and',
                            'condition' => array('column' => $this->entity['citl']['alias'] . '.language', 'comparison' => '=', 'value' => $response->result->set->getId()),
                        )
                    )
                );
            }
        }
        $response = $this->listContactInformaionTypes($filter, null, array('start' => 0, 'count' => 1));
        if ($response->error->exist) {
            return $response;
        }
        $response->stats->execution->start = $timeStamp;
        $response->stats->execution->end = time();
        $response->result->set = $response->result->set[0];

        return $response;
    }

    /**
     * @param $type
     *
     * @return array
     */
    public function insertContactInformationType($type) {
        return $this->insertContactInformationTypes(array($type));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\ContactInformationBundle\Services\ModelResponse|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function insertProductCategories(array $collection)
    {
        $timeStamp = time();
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countInserts = 0;
        $countLocalizations = 0;
        $insertedItems = array();
        $localizations = array();
        $now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\ContactInformationType) {
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            }
            else if (is_object($data)) {
                $entity = new BundleEntity\ProductCategory;
                if (!property_exists($data, 'date_added')) {
                    $data->date_added = $now;
                }
                if (!property_exists($data, 'date_updated')) {
                    $data->date_updated = $now;
                }
                foreach ($data as $column => $value) {
                    $localeSet = false;
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'local':
                            $localizations[$countInserts]['localizations'] = $value;
                            $localeSet = true;
                            $countLocalizations++;
                            break;
                        default:
                            $entity->$set($value);
                            break;
                    }
                    if ($localeSet) {
                        $localizations[$countInserts]['entity'] = $entity;
                    }
                }
                $this->em->persist($entity);
                $insertedItems[] = $entity;

                $countInserts++;
            }
        }
        /** Now handle localizations */
        if ($countInserts > 0 && $countLocalizations > 0) {
            $response = $this->insertContactInformationTypeocalizations($localizations);
        }
        if ($countInserts > 0) {
            $this->em->flush();
            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
    }

	/**
	 * @param $item
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
    public function updateContactInformationType($item){
        return $this->updateContactInformationTypes(array($item));
    }

	/**
	 * @param array $collection
	 *
	 * @return array|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
    public function updateContactInformationTypes(array $collection) {
	    $timeStamp = time();
	    if (!is_array($collection)) {
		    return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
	    }
	    $countUpdates = 0;
	    $updatedItems = array();
	    $localizations = array();
	    foreach ($collection as $data) {
		    if ($data instanceof BundleEntity\ContactInformationType) {
			    $entity = $data;
			    $this->em->persist($entity);
			    $updatedItems[] = $entity;
			    $countUpdates++;
		    } else if (is_object($data)) {
			    if (!property_exists($data, 'id') || !is_numeric($data->id)) {
				    return $this->createException('InvalidParameterException', 'Parameter must be an object with the "id" property and id property ​must have an integer value.', 'E:S:003');
			    }
			    if (!property_exists($data, 'date_updated')) {
				    $data->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
			    }
			    if (property_exists($data, 'date_added')) {
				    unset($data->date_added);
			    }
			    $response = $this->getContactInformationType($data->id);
			    if ($response->error->exist) {
				    return $this->createException('EntityDoesNotExist', 'Contact Information type with id / code ' . $data->id . ' does not exist in database.', 'E:D:002');
			    }
			    $oldEntity = $response->result->set;
			    foreach ($data as $column => $value) {
				    $set = 'set' . $this->translateColumnName($column);
				    switch ($column) {
					    case 'local':
						    foreach ($value as $langCode => $translation) {
							    $localization = $oldEntity->getLocalization($langCode, true);
							    $newLocalization = false;
							    if (!$localization) {
								    $newLocalization = true;
								    $localization = new BundleEntity\ContactInformationTypeLocalization();
								    $mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
								    $response = $mlsModel->getLanguage($langCode);
								    $localization->setLanguage($response->result->set);
								    $localization->setContactInformationType($oldEntity);
							    }
							    foreach ($translation as $transCol => $transVal) {
								    $transSet = 'set' . $this->translateColumnName($transCol);
								    $localization->$transSet($transVal);
							    }
							    if ($newLocalization) {
								    $this->em->persist($localization);
							    }
							    $localizations[] = $localization;
						    }
						    $oldEntity->setLocalizations($localizations);
						    break;
					    case 'id':
						    break;
					    default:
						    $oldEntity->$set($value);
						    break;
				    }
				    if ($oldEntity->isModified()) {
					    $this->em->persist($oldEntity);
					    $countUpdates++;
					    $updatedItems[] = $oldEntity;
				    }
			    }
		    }
	    }
	    if ($countUpdates > 0) {
		    $this->em->flush();
		    return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, time());
	    }
	    return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, time());
    }

	/**
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listPhoneNumbers(array $filter = null, array $sortOrder = null, array $limit = null){
		$timeStamp = time();
		if(!is_array($sortOrder) && !is_null($sortOrder)){
			return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
		}
		$oStr = $wStr = $gStr = $fStr = '';

		$qStr = 'SELECT '.$this->entity['pn']['alias']
			.' FROM '.$this->entity['pn']['name'].' '.$this->entity['pn']['alias'];

		if(!is_null($sortOrder)){
			foreach($sortOrder as $column => $direction){
				switch($column){
					case 'id':
					case 'area_code':
					case 'country_code':
					case 'number':
					case 'date_added':
					case 'date_removed':
					case 'date_updated':
						$column = $this->entity['pn']['alias'].'.'.$column;
						break;
				}
				$oStr .= ' '.$column.' '.strtoupper($direction).', ';
			}
			$oStr = rtrim($oStr, ', ');
			$oStr = ' ORDER BY '.$oStr.' ';
		}

		if(!is_null($filter)){
			$fStr = $this->prepareWhere($filter);
			$wStr .= ' WHERE '.$fStr;
		}

		$qStr .= $wStr.$gStr.$oStr;
		$q = $this->em->createQuery($qStr);
		$q = $this->addLimit($q, $limit);

		$result = $q->getResult();

		$totalRows = count($result);
		if ($totalRows < 1) {
			return $this->createException('InvalidParameterException', '$phone must have "country_code", "area_code" and "number" keys.', 'E:S:002');
		}
		return new ModelResponse($result, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}

	/**
	 * @param mixed $phone
	 *
	 * @return \BiberLtd\Bundle\ContactInformationBundle\Services\ModelResponse
	 */
	public function getPhoneNumber($phone)
	{
		$timeStamp = time();
		if ($phone instanceof BundleEntity\PhoneNumber) {
			return new ModelResponse($phone, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
		}
		$result = null;
		switch ($phone) {
			case is_numeric($phone):
				$result = $this->em->getRepository($this->entity['pn']['name'])->findOneBy(array('id' => $phone));
				break;
			case is_array($phone):
				if(!isset($phone['country_code']) || !isset($phone['area_code']) || !isset($phone['number'])){
					return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, time());
				}
				$result = $this->em->getRepository($this->entity['pn']['name'])->findOneBy(array('country_code' => $phone['country_code'], 'area_code' => $phone['area_code'], 'number' => $phone['number']));
				break;
		}
		if (is_null($result)) {
			return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, time());
		}

		return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}

	/**
	 * @param mixed $phone
	 *
	 * @return array
	 */
    public function insertPhoneNumber($phone){
        return $this->insertPhoneNumbers(array($phone));
    }
	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\ContactInformationBundle\Services\ModelResponse|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertPhoneNumbers(array $collection)
	{
		$timeStamp = time();
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$insertedItems = array();
		$now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\PhoneNumber) {
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			} else if (is_object($data)) {
				$entity = new BundleEntity\PhoneNumber();
				if (!property_exists($data, 'date_added')) {
					$data->date_added = $now;
				}
				if (!property_exists($data, 'date_updated')) {
					$data->date_updated = $now;
				}
				foreach ($data as $column => $value) {
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						default:
							$entity->$set($value);
							break;
					}
				}
				$this->em->persist($entity);
				$insertedItems[] = $entity;

				$countInserts++;
			}
		}
		if ($countInserts > 0) {
			$this->em->flush();
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
	}

	/**
	 * @param $phone
	 *
	 * @return array
	 */
    public function updatePhoneNumber($phone) {
        return $this->updatePhoneNumbers(array($phone));
    }

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\ContactInformationBundle\Services\ModelResponse
	 */
	public function updatePhoneNumbers(array $collection)
	{
		$timeStamp = time();
		$countUpdates = 0;
		$updatedItems = array();
		$now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\PhoneNumber) {
				$entity = $data;
				$this->em->persist($entity);
				$updatedItems[] = $entity;
				$countUpdates++;
			} else if (is_object($data)) {
				if (!property_exists($data, 'id') || !is_numeric($data->id)) {
					return $this->createException('InvalidParameter', 'Each data must contain a valid identifier id, integer', 'err.invalid.parameter.collection');
				}
				if (!property_exists($data, 'date_updated')) {
					$data->date_updated = $now;
				}
				if (property_exists($data, 'date_added')) {
					unset($data->date_added);
				}
				$response = $this->getPhoneNumber($data->id);
				if ($response->error->exist) {
					return $this->createException('EntityDoesNotExist', 'Phone number with id ' . $data->id, 'err.invalid.entity');
				}
				$oldEntity = $response->result->set;
				foreach ($data as $column => $value) {
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'id':
							break;
						default:
							$oldEntity->$set($value);
							break;
					}
					if ($oldEntity->isModified()) {
						$this->em->persist($oldEntity);
						$countUpdates++;
						$updatedItems[] = $oldEntity;
					}
				}
			}
		}
		if ($countUpdates > 0) {
			$this->em->flush();
			return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, time());
	}

	/**
	 * @param mixed $member
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\ContactInformationBundle\Services\ModelResponse
	 */
	public function listEmailAddressesOfMember($member, array $sortOrder = null, array $limit = null)
	{
		$timeStamp = time();
		$mModel = $this->kernel->getContainer()->get('membermanagement.model');
		$response = $mModel->getMember($member);
		if($response->error->exist){
			return $response;
		}
		$member = $response->result->set;

		$qStr = 'SELECT ' . $this->entity['eaom']['alias']
			. ' FROM ' . $this->entity['eaom']['name'] . ' ' . $this->entity['eaom']['alias']
			. ' WHERE ' . $this->entity['eaom']['alias'] . '.member = ' . $member->getId();

		$q = $this->em->createQuery($qStr);
		$result = $q->getResult();
		$selectedEmailIds = array();

		foreach ($result as $entity) {
			$selectedEmailIds[] = $entity->getId();
		}
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['ea']['alias'].'.id', 'comparison' => 'in', 'value' => $selectedEmailIds),
				),
			)
		);
		$response = $this->listEmailAddresses($filter, $sortOrder, $limit);

		$response->stats->execution->start = $timeStamp;
		$response->stats->execution->end = time();

		return $response;
	}

	/**
	 * @param mixed $member
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\ContactInformationBundle\Services\ModelResponse
	 */
	public function listPhoneNumbersOfMembers($member, array $sortOrder = null, array $limit = null)
	{
		$timeStamp = time();
		$mModel = $this->kernel->getContainer()->get('membermanagement.model');
		$response = $mModel->getMember($member);
		if($response->error->exist){
			return $response;
		}
		$member = $response->result->set;

		$qStr = 'SELECT ' . $this->entity['pnom']['alias']
			. ' FROM ' . $this->entity['pnom']['name'] . ' ' . $this->entity['pnom']['alias']
			. ' WHERE ' . $this->entity['pnom']['alias'] . '.member = ' . $member->getId();

		$q = $this->em->createQuery($qStr);
		$result = $q->getResult();
		$selectedIds = array();

		foreach ($result as $entity) {
			$selectedIds[] = $entity->getId();
		}
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['pn']['alias'].'.id', 'comparison' => 'in', 'value' => $selectedIds),
				),
			)
		);
		$response = $this->listEmailAddresses($filter, $sortOrder, $limit);

		$response->stats->execution->start = $timeStamp;
		$response->stats->execution->end = time();

		return $response;
	}

	/**
	 * @param array $collection
	 * @param mixed $member
	 *
	 * @return \BiberLtd\Bundle\ContactInformationBundle\Services\ModelResponse
	 */
	public function addPhoneNumbersToMember(array $collection, $member)
	{
		$timeStamp = time();
		$mModel = $this->kernel->getContainer()->get('membermanagement.model');
		$response = $mModel->getMember($member);
		if ($response->error->exist) {
			return $response;
		}
		$member = $response->result->set;
		$collection = array();
		$count = 0;
		$now = new \DateTime('now', new \DateTimezone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($collection as $phone) {
			$response = $this->getPhoneNumber($phone);
			if ($response->error->exist) {
				break;
			}
			$phoneEntity = $response->result->set;
			/** Check if association exists */
			if ($this->isPhoneAssociatedWithMember($phoneEntity, $member, true)) {
				break;
			}
			/** prepare object */
			$pnom = new BundleEntity\PhoneNumbersOfMember();
			$pnom->setMember($member)->setPhoneNumber($phoneEntity)->setDateAdded($now);
			/** persist entry */
			$this->em->persist($pnom);
			$collection[] = $pnom;
			$count++;
		}
		if ($count > 0) {
			$this->em->flush();
			return new ModelResponse($collection, $count, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
	}

	/**
	 * @param array $collection
	 * @param       $member
	 *
	 * @return \BiberLtd\Bundle\ContactInformationBundle\Services\ModelResponse
	 */
	public function addEmailAddressesToMember(array $collection, $member)
	{
		$timeStamp = time();
		$mModel = $this->kernel->getContainer()->get('membermanagement.model');
		$response = $mModel->getMember($member);
		if ($response->error->exist) {
			return $response;
		}
		$member = $response->result->set;
		$collection = array();
		$count = 0;
		$now = new \DateTime('now', new \DateTimezone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($collection as $email) {
			$response = $this->getEmailAddress($email);
			if ($response->error->exist) {
				break;
			}
			$emailEntity = $response->result->set;
			/** Check if association exists */
			if ($this->isEmailAddressAssociatedWithMember($emailEntity, $member, true)) {
				break;
			}
			/** prepare object */
			$eaom = new BundleEntity\EmailAddressesOfMember();
			$eaom->setMember($member)->setEmail($email)->setDateAdded($now);
			/** persist entry */
			$this->em->persist($eaom);
			$collection[] = $eaom;
			$count++;
		}
		if ($count > 0) {
			$this->em->flush();
			return new ModelResponse($collection, $count, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, time());
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, time());
	}

	/**
	 * @param      $email
	 * @param      $member
	 * @param bool $bypass
	 *
	 * @return \BiberLtd\Bundle\ContactInformationBundle\Services\ModelResponse|bool
	 */
	public function isEmailAddressAssociatedWithMember($email, $member, $bypass = false){
		$timeStamp = time();
		$response = $this->getEmailAddress($email);
		if ($response->error->exist) {
			return $response;
		}
		$email = $response->result->set;
		$mModel = $this->kernel->getContainer()->get('membermanagement.model');
		$response = $mModel->getMember($member);
		if ($response->error->exist) {
			return $response;
		}
		$member = $response->result->set;
		$found = false;
		unset($response);
		$qStr = 'SELECT COUNT(' . $this->entity['eaom']['alias'] . '.email)'
			. ' FROM ' . $this->entity['eaom']['name'] . ' ' . $this->entity['eaom']['alias']
			. ' WHERE ' . $this->entity['eaom']['alias'] . '.email_address = ' . $email->getId()
			. ' AND ' . $this->entity['eaom']['alias'] . '.member = ' . $member->getId();
		$query = $this->em->createQuery($qStr);

		$result = $query->getSingleScalarResult();

		if ($result > 0) {
			$found = true;
		}
		if ($bypass) {
			return $found;
		}
		return new ModelResponse($found, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
	/**
	 * @param      $email
	 * @param      $member
	 * @param bool $bypass
	 *
	 * @return \BiberLtd\Bundle\ContactInformationBundle\Services\ModelResponse|bool
	 */
	public function isPhoneNumberAssociatedWithMember($phone, $member, $bypass = false){
		$timeStamp = time();
		$response = $this->getPhoneNumber($phone);
		if ($response->error->exist) {
			return $response;
		}
		$phone = $response->result->set;
		$mModel = $this->kernel->getContainer()->get('membermanagement.model');
		$response = $mModel->getMember($member);
		if ($response->error->exist) {
			return $response;
		}
		$member = $response->result->set;
		$found = false;
		unset($response);
		$qStr = 'SELECT COUNT(' . $this->entity['pnom']['alias'] . '.phone_number)'
			. ' FROM ' . $this->entity['pnom']['name'] . ' ' . $this->entity['pnom']['alias']
			. ' WHERE ' . $this->entity['pnom']['alias'] . '.phone_number = ' . $email->getId()
			. ' AND ' . $this->entity['pnom']['alias'] . '.member = ' . $member->getId();
		$query = $this->em->createQuery($qStr);

		$result = $query->getSingleScalarResult();

		if ($result > 0) {
			$found = true;
		}
		if ($bypass) {
			return $found;
		}
		return new ModelResponse($found, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, time());
	}
}