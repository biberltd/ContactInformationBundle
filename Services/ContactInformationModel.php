<?php

/**
 * ContactInformationModel Class
 *
 * This class acts as a database proxy model for ContactInformationBundle functionalities.
 *
 * @package	Core\Bundles\ContactInformationBundle
 * @subpackage	Services
 * @name	ContactInformationModel
 *
 * @author      Said İmamoğlu
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.0.5
 * @date        03.04.2014
 *
 * =============================================================================================================
 * !! INSTRUCTIONS ON IMPORTANT ASPECTS OF MODEL METHODS !!!
 *
 * Each model function must return a $response ARRAY.
 * The array must contain the following keys and corresponding values.
 *
 * $response = array(
 *              'result'    =>   An array that contains the following keys:
 *                               'set'         Actual result set returned from ORM or null
 *                               'total_rows'  0 or number of total rows
 *                               'last_insert_id' The id of the item that is added last (if insert action)
 *              'error'     =>   true if there is an error; false if there is none.
 *              'code'      =>   null or a semantic and short English string that defines the error concanated
 *                               with dots, prefixed with err and the initials of the name of model class.
 *                               EXAMPLE: err.amm.action.not.found success messages have a prefix called scc..
 *
 *                               NOTE: DO NOT FORGET TO ADD AN ENTRY FOR ERROR CODE IN BUNDLE'S
 *                               RESOURCES/TRANSLATIONS FOLDER FOR EACH LANGUAGE.
 * =============================================================================================================
 * TODOs:
 * Do not forget to implement ORDER, AND PAGINATION RELATED FUNCTIONALITY
 *
 * @todo v1.0.0     listPhoneNumbersOfMembers()
 * @todo v1.1.0     listPhoneNumbersWithAreaCode()
 * @todo v1.1.0     listPhoneNumbersWithCountryCode()
 * @todo v1.1.0     listPhoneNumbersWithAreaAndCountryCode()
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
     * @name            __construct()
     *                  Constructor.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param           object          $kernel
     * @param           string          $db_connection  Database connection key as set in app/config.yml
     * @param           string          $orm            ORM that is used.
     */
    public function __construct($kernel, $db_connection = 'default', $orm = 'doctrine') {
        parent::__construct($kernel, $db_connection, $orm);
        /**
         * Register entity names for easy reference.
         */
        $this->entity = array(
            'contact_information_type' => array('name' => 'ContactInformationBundle:ContactInformationType', 'alias' => 'cit'),
            'contact_information_type_localization' => array('name' => 'ContactInformationBundle:ContactInformationTypeLocalization', 'alias' => 'citl'),
            'email_address' => array('name' => 'ContactInformationBundle:EmailAddress', 'alias' => 'ea'),
            'email_addresses_of_member' => array('name' => 'ContactInformationBundle:EmailAddressesOfMember', 'alias' => 'eaom'),
            'phone_number' => array('name' => 'ContactInformationBundle:PhoneNumber', 'alias' => 'pn'),
            'phone_numbers_of_member' => array('name' => 'ContactInformationBundle:PhoneNumbersOfMember', 'alias' => 'pnom'),
            'social_account' => array('name' => 'ContactInformationBundle:SocialAccount', 'alias' => 'sc'),
        );
    }

    /**
     * @name            __destruct()
     *                  Destructor.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     */
    public function __destruct() {
        foreach ($this as $property => $value) {
            $this->$property = null;
        }
    }

    /**
     * @name 		deleteEmailAddress()
     * Deletes an existing email address from database.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->deleteEmailAddresses()
     *
     * @param           mixed           $item           Entity, id or url key of item
     * @param           string          $by
     *
     * @return          mixed           $response
     */
    public function deleteEmailAddress($item, $by = 'entity') {
        return $this->deleteEmailAddresses(array($item), $by);
    }

    /**
     * @name            deleteGalleries()
     * Deletes provided email addresses from database.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array           $collection     Collection of Item entities, ids, or codes or url keys
     * @param           string          $by             Accepts the following options: entity, id, code, url_key
     *
     * @return          array           $response
     */
    public function deleteEmailAddresses($collection, $by = 'entity') {
        $this->resetResponse();
        $by_opts = array('entity', 'id', 'url_key');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', 'err.invalid.parameter.collection', implode(',', $by_opts));
        }
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'Array');
        }
        $entries = array();
        /** Loop through items and collect values. */
        $delete_count = 0;
        foreach ($collection as $item) {
            $value = '';
            if (is_object($item)) {
                if (!$item instanceof BundleEntity\EmailAddress) {
                    return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'BundleEntity\EmailAddress');
                }
                $this->em->remove($item);
                $delete_count++;
            } else if (is_numeric($item) || is_string($item)) {
                $value = $item;
            } else {
                /** If array values are not numeric nor object */
                return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'integer, string, or Module entity');
            }
            if (!empty($value) && $value != '') {
                $entries[] = $value;
            }
        }
        /**
         * Control if there is any entity ids in collection.
         */
        if (count($entries) < 1) {
            return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'Array');
        }
        $join_needed = false;
        /**
         * Prepare query string.
         */
        switch ($by) {
            case 'entity':
                /** Flush to delete all persisting objects */
                $this->em->flush();
                /**
                 * Prepare & Return Response
                 */
                $this->response = array(
	    'rowCount' => $this->response['rowCount'],
                    'result' => array(
                        'set' => null,
                        'total_rows' => $delete_count,
                        'last_insert_id' => null,
                    ),
                    'error' => false,
                    'code' => 'scc.db.delete.done',
                );
                return $this->response;
            case 'id':
                $values = implode(',', $entries);
                break;
            /** Requires JOIN */
            case 'url_key':
                $join_needed = true;
                $values = implode('\',\'', $entries);
                $values = '\'' . $values . '\'';
                break;
        }
        if ($join_needed) {
            $q_str = 'DELETE ' . $this->entity['table']['alias']
                    . ' FROM ' . $this->entity['table_localization']['name'] . ' ' . $this->entity['table_localization']['alias']
                    . ' JOIN ' . $this->entity['table_localization']['name'] . ' ' . $this->entity['table_localization']['alias']
                    . ' WHERE ' . $this->entity['table_localization']['alias'] . '.' . $by . ' IN(:values)';
        } else {
            $q_str = 'DELETE ' . $this->entity['email_address']['alias']
                    . ' FROM ' . $this->entity['email_address']['name'] . ' ' . $this->entity['email_address']['alias']
                    . ' WHERE ' . $this->entity['email_address']['alias'] . '.' . $by . ' IN(:values)';
        }
        /**
         * Create query object.
         */
        $query = $this->em->createQuery($q_str);
        $query->setParameter('values', $entries);
        /**
         * Free memory.
         */
        unset($values);
        /**
         * 6. Run query
         */
        $query->getResult();
        /**
         * Prepare & Return Response
         */
        $this->response = array(
	    'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $entries,
                'total_rows' => count($entries),
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.delete.done',
        );
        return $this->response;
    }

    /**
     * @name 		deleteContactInformationType()
     * Deletes an existing contact information type  from database.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->deleteContactInformationTypes()
     *
     * @param           mixed           $item           Entity, id or url key of item
     * @param           string          $by
     *
     * @return          mixed           $response
     */
    public function deleteContactInformationType($item, $by = 'entity') {
        return $this->deleteContactInformationTypes(array($item), $by);
    }

    /**
     * @name 		deleteContactInformationTypes()
     * Deletes provided  contact information types from database.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array           $collection     Collection of Item entities, ids, or codes or url keys
     * @param           string          $by             Accepts the following options: entity, id, code, url_key
     *
     * @return          array           $response
     */
    public function deleteContactInformationTypes($collection, $by = 'entity') {
        $this->resetResponse();
        $by_opts = array('entity', 'id', 'url_key');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', 'err.invalid.parameter.collection', implode(',', $by_opts));
        }
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'Array');
        }
        $entries = array();
        /** Loop through items and collect values. */
        $delete_count = 0;
        foreach ($collection as $item) {
            $value = '';
            if (is_object($item)) {
                if (!$item instanceof BundleEntity\ContactInformationType) {
                    return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'BundleEntity\ContactInformationType');
                }
                $this->em->remove($item);
                $delete_count++;
            } else if (is_numeric($item) || is_string($item)) {
                $value = $item;
            } else {
                /** If array values are not numeric nor object */
                return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'integer, string, or Module entity');
            }
            if (!empty($value) && $value != '') {
                $entries[] = $value;
            }
        }
        /**
         * Control if there is any entity ids in collection.
         */
        if (count($entries) < 1) {
            return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'Array');
        }
        $join_needed = false;
        /**
         * Prepare query string.
         */
        switch ($by) {
            case 'entity':
                /** Flush to delete all persisting objects */
                $this->em->flush();
                /**
                 * Prepare & Return Response
                 */
                $this->response = array(
	    'rowCount' => $this->response['rowCount'],
                    'result' => array(
                        'set' => null,
                        'total_rows' => $delete_count,
                        'last_insert_id' => null,
                    ),
                    'error' => false,
                    'code' => 'scc.db.delete.done',
                );
                return $this->response;
            case 'id':
                $values = implode(',', $entries);
                break;
            /** Requires JOIN */
            case 'url_key':
                $join_needed = true;
                $values = implode('\',\'', $entries);
                $values = '\'' . $values . '\'';
                break;
        }
        if ($join_needed) {
            $q_str = 'DELETE ' . $this->entity['contact_information_type']['alias']
                    . ' FROM ' . $this->entity['contact_information_type_localization']['name'] . ' ' . $this->entity['contact_information_type_localization']['alias']
                    . ' JOIN ' . $this->entity['contact_information_type_localization']['name'] . ' ' . $this->entity['contact_information_type_localization']['alias']
                    . ' WHERE ' . $this->entity['contact_information_type_localization']['alias'] . '.' . $by . ' IN(:values)';
        } else {
            $q_str = 'DELETE ' . $this->entity['contact_information_type']['alias']
                    . ' FROM ' . $this->entity['contact_information_type']['name'] . ' ' . $this->entity['table']['alias']
                    . ' WHERE ' . $this->entity['contact_information_type']['alias'] . '.' . $by . ' IN(:values)';
        }
        /**
         * Create query object.
         */
        $query = $this->em->createQuery($q_str);
        $query->setParameter('values', $entries);
        /**
         * Free memory.
         */
        unset($values);
        /**
         * 6. Run query
         */
        $query->getResult();
        /**
         * Prepare & Return Response
         */
        $this->response = array(
	    'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $entries,
                'total_rows' => count($entries),
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.delete.done',
        );
        return $this->response;
    }

    /**
     * @name 		deletePhoneNumber()
     * Deletes an existing phone number from database.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->deletePhoneNumbers()
     *
     * @param           mixed           $item           Entity, id or url key of item
     * @param           string          $by
     *
     * @return          mixed           $response
     */
    public function deletePhoneNumber($item, $by = 'entity') {
        return $this->deletePhoneNumbers(array($item), $by);
    }

    /**
     * @name            deletePhoneNumbers()
     * Deletes provided phone numbers from database.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array           $collection     Collection of Item entities, ids, or codes or url keys
     * @param           string          $by             Accepts the following options: entity, id, code, url_key
     *
     * @return          array           $response
     */
    public function deletePhoneNumbers($collection, $by = 'entity') {
        $this->resetResponse();
        $by_opts = array('entity', 'id', 'url_key');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', 'err.invalid.parameter.collection', implode(',', $by_opts));
        }
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'Array');
        }
        $entries = array();
        /** Loop through items and collect values. */
        $delete_count = 0;
        foreach ($collection as $item) {
            $value = '';
            if (is_object($item)) {
                if (!$item instanceof BundleEntity\PhoneNumber) {
                    return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'BundleEntity\PhoneNumber');
                }
                $this->em->remove($item);
                $delete_count++;
            } else if (is_numeric($item) || is_string($item)) {
                $value = $item;
            } else {
                /** If array values are not numeric nor object */
                return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'integer, string, or Module entity');
            }
            if (!empty($value) && $value != '') {
                $entries[] = $value;
            }
        }
        /**
         * Control if there is any entity ids in collection.
         */
        if (count($entries) < 1) {
            return $this->createException('InvalidParameterException', 'err.invalid.parameter.collection', 'Array');
        }
        $join_needed = false;
        /**
         * Prepare query string.
         */
        switch ($by) {
            case 'entity':
                /** Flush to delete all persisting objects */
                $this->em->flush();
                /**
                 * Prepare & Return Response
                 */
                $this->response = array(
	    'rowCount' => $this->response['rowCount'],
                    'result' => array(
                        'set' => null,
                        'total_rows' => $delete_count,
                        'last_insert_id' => null,
                    ),
                    'error' => false,
                    'code' => 'scc.db.delete.done',
                );
                return $this->response;
            case 'id':
                $values = implode(',', $entries);
                break;
            /** Requires JOIN */
            case 'url_key':
                $join_needed = true;
                $values = implode('\',\'', $entries);
                $values = '\'' . $values . '\'';
                break;
        }
        if ($join_needed) {
            $q_str = 'DELETE ' . $this->entity['table']['alias']
                    . ' FROM ' . $this->entity['table_localization']['name'] . ' ' . $this->entity['table_localization']['alias']
                    . ' JOIN ' . $this->entity['table_localization']['name'] . ' ' . $this->entity['table_localization']['alias']
                    . ' WHERE ' . $this->entity['table_localization']['alias'] . '.' . $by . ' IN(:values)';
        } else {
            $q_str = 'DELETE ' . $this->entity['phone_number']['alias']
                    . ' FROM ' . $this->entity['phone_number']['name'] . ' ' . $this->entity['phone_number']['alias']
                    . ' WHERE ' . $this->entity['phone_number']['alias'] . '.' . $by . ' IN(:values)';
        }
        /**
         * Create query object.
         */
        $query = $this->em->createQuery($q_str);
        $query->setParameter('values', $entries);
        /**
         * Free memory.
         */
        unset($values);
        /**
         * 6. Run query
         */
        $query->getResult();
        /**
         * Prepare & Return Response
         */
        $this->response = array(
	    'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $entries,
                'total_rows' => count($entries),
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.delete.done',
        );
        return $this->response;
    }

    /**
     * @name 		doesContactInformationTypeExist()
     *  		Checks if entry exists in database.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->getContactInformationType()
     *
     * @param           mixed           $data           id, url_key
     * @param           string          $by             id, url_key
     *
     * @param           bool            $bypass         If set to true does not return response but only the result.
     *
     * @return          mixed           $response
     */
    public function doesContactInformationTypeExist($item, $by = 'id', $bypass = false) {
        $this->resetResponse();
        $exist = false;

        $response = $this->getContactInformationType($item, $by);

        if (!$response['error'] && $response['result']['total_rows'] > 0) {
            $exist = $response['result']['set'];
            $error = false;
        } else {
            $exist = false;
            $error = true;
        }

        if ($bypass) {
            return $exist;
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
	    'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $exist,
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => $error,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 		doesEmailAddressExist()
     *  		Checks if entry exists in database.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->getEmailAddress()
     *
     * @param           mixed           $item           id, url_key
     * @param           string          $by             id, url_key
     *
     * @param           bool            $bypass         If set to true does not return response but only the result.
     *
     * @return          mixed           $response
     */
    public function doesEmailAddressExist($item, $by = 'id', $bypass = false) {
        $this->resetResponse();
        $exist = false;

        $response = $this->getEmailAddress($item, $by);

        if (!$response['error'] && $response['result']['total_rows'] > 0) {
            $exist = $response['result']['set'];
            $error = false;
        } else {
            $exist = false;
            $error = true;
        }

        if ($bypass) {
            return $exist;
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
	    'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $exist,
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => $error,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name            listEmailAddresses()
     * List items of a given collection.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->resetResponse()
     * @use             $this->createException()
     * @use             $this->prepare_where()
     * @use             $this->createQuery()
     * @use             $this->getResult()
     * 
     * @throws          InvalidSortOrderException
     * @throws          InvalidLimitException
     * 
     *
     * @param           mixed           $filter                Multi dimensional array
     * @param           array           $sortorder              Array
     *                                                              'column'    => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     * @param           string           $query_str             If a custom query string needs to be defined.
     *
     * @return          array           $response
     */
    public function listEmailAddresses($filter = null, $sortorder = null, $limit = null, $query_str = null) {
        $this->resetResponse();
        if (!is_array($sortorder) && !is_null($sortorder)) {
            return $this->createException('InvalidSortOrderException', '', 'err.invalid.parameter.sortorder');
        }

        /**
         * Add filter check to below to set join_needed to true
         */
        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';


        /**
         * Start creating the query
         *
         * Note that if no custom select query is provided we will use the below query as a start
         */
        $localizable = false;
        if (is_null($query_str)) {
            if ($localizable) {
                $query_str = 'SELECT ' . $this->entity['table_localization']['alias']
                        . ' FROM ' . $this->entity['table_localization']['name'] . ' ' . $this->entity['table_localization']['alias']
                        . ' JOIN ' . $this->entity['table_localization']['alias'] . '.COLUMN ' . $this->entity['table']['alias'];
            } else {
                $query_str = 'SELECT ' . $this->entity['email_address']['alias']
                        . ' FROM ' . $this->entity['email_address']['name'] . ' ' . $this->entity['email_address']['alias'];
            }
        }
        /*
         * Prepare ORDER BY section of query
         */
        if (!is_null($sortorder)) {
            foreach ($sortorder as $column => $direction) {
                switch ($column) {
                    case 'id':
                    case 'name':
                    case 'url_key':
                        break;
                }
                $order_str .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY ' . $order_str . ' ';
        }

        /*
         * Prepare WHERE section of query
         */

        if (!is_null($filter)) {
            $filter_str = $this->prepare_where($filter);
            $where_str = ' WHERE ' . $filter_str;
        }



        $query_str .= $where_str . $group_str . $order_str;


        $query = $this->em->createQuery($query_str);

        /*
         * Prepare LIMIT section of query
         */

        if (!is_null($limit) && is_numeric($limit)) {
            /*
             * if limit is set
             */
            if (isset($limit['start']) && isset($limit['count'])) {
                $query = $this->addLimit($query, $limit);
            } else {
                $this->createException('InvalidLimitException', '', 'err.invalid.limit');
            }
        }
        //print_r($query->getSql()); die;
        /*
         * Prepare and Return Response
         */

        $files = $query->getResult();


        $total_rows = count($files);
        if ($total_rows < 1) {
            $this->response['error'] = true;
            $this->response['code'] = 'err.db.entry.notexist';
            return $this->response;
        }

        $this->response = array(
	    'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $files,
                'total_rows' => $total_rows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );

        return $this->response;
    }

    /**
     * @name 		getEmailAddress()
     * Returns details of a gallery.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     * @use             $this->listItems()
     *
     * @param           mixed           $item               id, url_key
     * @param           string          $by                 entity, id, url_key
     *
     * @return          mixed           $response
     */
    public function getEmailAddress($item, $by = 'id') {
        $this->resetResponse();
        $by_opts = array('id', 'url_key');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if (!is_object($item) && !is_numeric($item) && !is_string($item)) {
            return $this->createException('InvalidParameterException', 'Item', 'err.invalid.parameter.gallery');
        }
        if (is_object($item)) {
            if (!$item instanceof BundleEntity\EmailAddress) {
                return $this->createException('InvalidParameterException', 'Item', 'err.invalid.parameter.gallery');
            }
            /**
             * Prepare & Return Response
             */
            $this->response = array(
	    'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => $item,
                    'total_rows' => 1,
                    'last_insert_id' => null,
                ),
                'error' => false,
                'code' => 'scc.db.entry.exist',
            );
            return $this->response;
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['email_address']['alias'] . '.' . $by, 'comparison' => '=', 'value' => $item),
                )
            )
        );

        $response = $this->listEmailAddresses($filter, null, array('start' => 0, 'count' => 1));
        if ($response['error']) {
            return $response;
        }
        $collection = $response['result']['set'];
        /**
         * Prepare & Return Response
         */
        $this->response = array(
	    'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

     /**
     * @name 		insertEmailAddress()
     * Inserts one or more item into database.
     *
     * @since		1.0.1
     * @version         1.0.3
     * @author          Said İmamoğlu
     *
     * @use             $this->insertFiles()
     *
     * @param           array           $item        Collection of entities or post data.
     *
     * @return          array           $response
     */
    
    public function insertEmailAddress($item,$by = 'post') {
        $this->resetResponse();
        return $this->insertEmailAddresses($item);
    }
    /**
     * @name            insertEmailAddresses()
     * Inserts one or more items into database.
     *
     * @since           1.0.1
     * @version         1.0.3
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @throws          InvalidParameterException
     * @throws          InvalidMethodException
     *
     * @param           array           $collection        Collection of entities or post data.
     * @param           string          $by                entity, post
     *
     * @return          array           $response
     */
    public function insertEmailAddresses($collection, $by = 'post') {
        /* Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterException', 'array() or Integer', 'err.invalid.parameter.collection');
        }

        if (!in_array($by, $this->by_opts)) {
            return $this->createException('InvalidParameterException', implode(',', $this->by_opts), 'err.invalid.parameter.by.collection');
        }

        if ($by == 'entity') {
            $sub_response = $this->insert_entities($collection, 'BiberLtd\\Core\\Bundles\\ContactInformationBundle\\Entity\\EmailAddress');
        } elseif ($by == 'post') {

            foreach ($collection as $item) {
                $entity = new \BiberLtd\Bundle\ContactInformationBundle\Entity\EmailAddress();
                foreach ($item['address'] as $column => $value) {
                    $itemMethod = 'set_' . $column;
                    if (method_exists($entity, $itemMethod)) {
                        $entity->$itemMethod($value);
                    } else {
                        return $this->createException('InvalidMethodException', 'method not found in entity', 'err.method.notfound');
                    }
                }
                unset($item, $column, $value);
                $this->em->persist($entity);
            }
            $this->em->flush();
            $this->response = array(
	    'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => $collection,
                    'total_rows' => count($collection),
                    'last_insert_id' => $entity->getId(),
                ),
                'error' => false,
                'code' => 'scc.db.insert.done',
            );

            return $this->response;
        }
    }
    
    /*
     * @name            updateEmailAddress()
     * Updates single item. The item must be either a post data (array) or an entity
     * 
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     * 
     * @use             $this->resetResponse()
     * @use             $this->updateEmailAddresses()
     * 
     * @param           mixed   $item     Entity or Entity id of a folder
     * 
     * @return          array   $response
     * 
     */

    public function updateEmailAddress($item) {
        $this->resetResponse();
        return $this->updateEmailAddresses(array($address));
    }

    /*
     * @name            updateEmailAddresses()
     * Updates one or more item details in database.
     * 
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     * 
     * @use             $this->update_entities()
     * @use             $this->createException()
     * @use             $this->listEmailAddresses()
     * 
     * 
     * @throws          InvalidParameterException
     * 
     * @param           array   $collection     Collection of item's entities or array of entity details.
     * @param           array   $by             entity or post
     * 
     * @return          array   $response
     * 
     */

    public function updateEmailAddresses($collection, $by = 'post') {
        if ($by == 'entity') {
            $sub_response = $this->update_entities($collection, 'BundleEntity\Item');
            /**
             * If there are items that cannot be deleted in the collection then $sub_Response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if ($sub_response['process'] == 'stop') {
                $this->response = array(
	    'rowCount' => $this->response['rowCount'],
                    'result' => array(
                        'set' => $sub_response['entries']['valid'],
                        'total_rows' => $sub_response['item_count'],
                        'last_insert_id' => null,
                    ),
                    'error' => false,
                    'code' => 'scc.db.delete.done',
                );
                return $this->response;
            } else {
                $collection = $sub_response['entries']['invalid'];
            }
        } elseif ($by == 'post') {
            if (!is_array($collection)) {
                return $this->createException('InvalidParameterException', 'expected an array', 'err.invalid.by');
            }

            $itemsToUpdate = array();
            $itemId = array();
            $count = 0;

            foreach ($collection as $item) {
                if (!isset($item['id'])) {
                    unset($collection[$count]);
                }
                $itemId[] = $item['id'];
                $itemsToUpdate[$item['id']] = $item;
                $count++;
            }
            $filter = array(
                array(
                    'glue' => 'and',
                    'condition' => array(
                        array(
                            'glue' => 'and',
                            'condition' => array('column' => $this->entity['email_address']['alias'] . '.id', 'comparison' => 'in', 'value' => $itemId),
                        )
                    )
                )
            );
            $response = $this->listEmailAddresses($filter);
            if ($response['error']) {
                return $this->createException('InvalidParameterException', 'Array', 'err.invalid.parameter.collection');
            }

            $entities = $response['result']['set'];

            foreach ($entities as $entity) {
                $itemData = $itemsToUpdate[$entity->getId()];
                foreach ($itemData as $column => $value) {
                    $itemMethodSet = 'set_' . $column;
                    $entity->$itemMethodSet($value);
                }
                $this->em->persist($entity);
            }
            $this->em->flush();
        }
    }
   
    /**
     * @name            listContactInformationTypes()
     * List items of a given collection.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->resetResponse()
     * @use             $this->createException()
     * @use             $this->prepare_where()
     * @use             $this->createQuery()
     * @use             $this->getResult()
     * 
     * @throws          InvalidSortOrderException
     * @throws          InvalidLimitException
     * 
     *
     * @param           mixed           $filter                Multi dimensional array
     * @param           array           $sortorder              Array
     *                                                              'column'    => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     * @param           string           $query_str             If a custom query string needs to be defined.
     *
     * @return          array           $response
     */

    public function listContactInformationTypes($filter = null, $sortorder = null, $limit = null, $query_str = null) {
        $this->resetResponse();
        if (!is_array($sortorder) && !is_null($sortorder)) {
            return $this->createException('InvalidSortOrderException', '', 'err.invalid.parameter.sortorder');
        }

        /**
         * Add filter check to below to set join_needed to true
         */
        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';


        /**
         * Start creating the query
         *
         * Note that if no custom select query is provided we will use the below query as a start
         */
        $localizable = true;
        if (is_null($query_str)) {
            if ($localizable) {
                $query_str = 'SELECT ' . $this->entity['contact_information_type_localization']['alias']
                        . ' FROM ' . $this->entity['contact_information_type_localization']['name'] . ' ' . $this->entity['contact_information_type_localization']['alias']
                        . ' JOIN ' . $this->entity['contact_information_type_localization']['alias'] . '.COLUMN ' . $this->entity['contact_information_type']['alias'];
            } else {
                $query_str = 'SELECT ' . $this->entity['table']['alias']
                        . ' FROM ' . $this->entity['table']['name'] . ' ' . $this->entity['table']['alias'];
            }
        }
        /*
         * Prepare ORDER BY section of query
         */
        if (!is_null($sortorder)) {
            foreach ($sortorder as $column => $direction) {
                switch ($column) {
                    case 'id':
                    case 'name':
                    case 'url_key':
                        break;
                }
                $order_str .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY ' . $order_str . ' ';
        }

        /*
         * Prepare WHERE section of query
         */

        if (!is_null($filter)) {
            $filter_str = $this->prepare_where($filter);
            $where_str = ' WHERE ' . $filter_str;
        }



        $query_str .= $where_str . $group_str . $order_str;


        $query = $this->em->createQuery($query_str);

        /*
         * Prepare LIMIT section of query
         */

        if (!is_null($limit) && is_numeric($limit)) {
            /*
             * if limit is set
             */
            if (isset($limit['start']) && isset($limit['count'])) {
                $query = $this->addLimit($query, $limit);
            } else {
                $this->createException('InvalidLimitException', '', 'err.invalid.limit');
            }
        }
        //print_r($query->getSql()); die;
        /*
         * Prepare and Return Response
         */

        $files = $query->getResult();


        $total_rows = count($files);
        if ($total_rows < 1) {
            $this->response['error'] = true;
            $this->response['code'] = 'err.db.entry.notexist';
            return $this->response;
        }

        $this->response = array(
	    'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $files,
                'total_rows' => $total_rows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );

        return $this->response;
    }

    /**
     * @name 		getContactInformationType()
     * Returns details of a gallery.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     * @use             $this->listContactInformationTypes()
     *
     * @param           mixed           $item               id, url_key
     * @param           string          $by                 entity, id, url_key
     *
     * @return          mixed           $response
     */
    public function getContactInformationType($item, $by = 'id') {
        $this->resetResponse();
        $by_opts = array('id', 'url_key');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if (!is_object($item) && !is_numeric($item) && !is_string($item)) {
            return $this->createException('InvalidParameterException', 'Item', 'err.invalid.parameter.gallery');
        }
        if (is_object($item)) {
            if (!$item instanceof BundleEntity\ContactInformationType) {
                return $this->createException('InvalidParameterException', 'Item', 'err.invalid.parameter.gallery');
            }
            /**
             * Prepare & Return Response
             */
            $this->response = array(
	    'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => $item,
                    'total_rows' => 1,
                    'last_insert_id' => null,
                ),
                'error' => false,
                'code' => 'scc.db.entry.exist',
            );
            return $this->response;
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['contact_information_type_localization']['alias'] . '.' . $by, 'comparison' => '=', 'value' => $item),
                )
            )
        );

        $response = $this->listContactInformationTypes($filter, null, array('start' => 0, 'count' => 1));
        if ($response['error']) {
            return $response;
        }
        $collection = $response['result']['set'];
        /**
         * Prepare & Return Response
         */
        $this->response = array(
	    'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

     /**
     * @name 		insertContactInformationType()
     * Inserts one or more item into database.
     *
     * @since		1.0.1
     * @version         1.0.3
     * @author          Said İmamoğlu
     *
     * @use             $this->insertFiles()
     *
     * @param           array           $item        Collection of entities or post data.
     *
     * @return          array           $response
     */
    
    public function insertContactInformationType($item,$by = 'post') {
        $this->resetResponse();
        return $this->insertContactInformationTypes($item);
    }
    /**
     * @name            insertContactInformationTypes()
     * Inserts one or more items into database.
     *
     * @since           1.0.1
     * @version         1.0.3
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @throws          InvalidParameterException
     * @throws          InvalidMethodException
     *
     * @param           array           $collection        Collection of entities or post data.
     * @param           string          $by                entity, post
     *
     * @return          array           $response
     */
    public function insertContactInformationTypes($collection, $by = 'post') {
        /* Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterException', 'array() or Integer', 'err.invalid.parameter.collection');
        }

        if (!in_array($by, $this->by_opts)) {
            return $this->createException('InvalidParameterException', implode(',', $this->by_opts), 'err.invalid.parameter.by.collection');
        }

        if ($by == 'entity') {
            $sub_response = $this->insert_entities($collection, 'BiberLtd\\Core\\Bundles\\ContactInformationBundle\\Entity\\ContactInformationType');
        } elseif ($by == 'post') {

            foreach ($collection as $item) {
                $entity = new \BiberLtd\Bundle\ContactInformationBundle\Entity\ContactInformationType();
                foreach ($item['address'] as $column => $value) {
                    $itemMethod = 'set_' . $column;
                    if (method_exists($entity, $itemMethod)) {
                        $entity->$itemMethod($value);
                    } else {
                        return $this->createException('InvalidMethodException', 'method not found in entity', 'err.method.notfound');
                    }
                }
                unset($item, $column, $value);
                $this->em->persist($entity);
            }
            $this->em->flush();
            $this->response = array(
	    'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => $collection,
                    'total_rows' => count($collection),
                    'last_insert_id' => $entity->getId(),
                ),
                'error' => false,
                'code' => 'scc.db.insert.done',
            );

            return $this->response;
        }
    }
    /*
     * @name            updateContactInformationType()
     * Updates single item. The item must be either a post data (array) or an entity
     * 
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     * 
     * @use             $this->resetResponse()
     * @use             $this->updateContactInformationTypes()
     * 
     * @param           mixed   $item     Entity or Entity id of a folder
     * 
     * @return          array   $response
     * 
     */

    public function updateContactInformationType($item) {
        $this->resetResponse();
        return $this->updateContactInformationTypes(array($address));
    }

    /*
     * @name            updateContactInformationTypes()
     * Updates one or more item details in database.
     * 
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     * 
     * @use             $this->update_entities()
     * @use             $this->createException()
     * @use             $this->listContactInformationTypes()
     * 
     * 
     * @throws          InvalidParameterException
     * 
     * @param           array   $collection     Collection of item's entities or array of entity details.
     * @param           array   $by             entity or post
     * 
     * @return          array   $response
     * 
     */

    public function updateContactInformationTypes($collection, $by = 'post') {
        if ($by == 'entity') {
            $sub_response = $this->update_entities($collection, 'BundleEntity\Item');
            /**
             * If there are items that cannot be deleted in the collection then $sub_Response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if ($sub_response['process'] == 'stop') {
                $this->response = array(
	    'rowCount' => $this->response['rowCount'],
                    'result' => array(
                        'set' => $sub_response['entries']['valid'],
                        'total_rows' => $sub_response['item_count'],
                        'last_insert_id' => null,
                    ),
                    'error' => false,
                    'code' => 'scc.db.delete.done',
                );
                return $this->response;
            } else {
                $collection = $sub_response['entries']['invalid'];
            }
        } elseif ($by == 'post') {
            if (!is_array($collection)) {
                return $this->createException('InvalidParameterException', 'expected an array', 'err.invalid.by');
            }

            $itemsToUpdate = array();
            $itemId = array();
            $count = 0;

            foreach ($collection as $item) {
                if (!isset($item['id'])) {
                    unset($collection[$count]);
                }
                $itemId[] = $item['id'];
                $itemsToUpdate[$item['id']] = $item;
                $count++;
            }
            $filter = array(
                array(
                    'glue' => 'and',
                    'condition' => array(
                        array(
                            'glue' => 'and',
                            'condition' => array('column' => $this->entity['contact_information_type']['alias'] . '.id', 'comparison' => 'in', 'value' => $itemId),
                        )
                    )
                )
            );
            $response = $this->listContactInformationTypes($filter);
            if ($response['error']) {
                return $this->createException('InvalidParameterException', 'Array', 'err.invalid.parameter.collection');
            }

            $entities = $response['result']['set'];

            foreach ($entities as $entity) {
                $itemData = $itemsToUpdate[$entity->getId()];
                foreach ($itemData as $column => $value) {
                    $itemMethodSet = 'set_' . $column;
                    $entity->$itemMethodSet($value);
                }
                $this->em->persist($entity);
            }
            $this->em->flush();
        }
    }
   
    /**
     * @name            listPhoneNumbers()
     * List items of a given collection.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->resetResponse()
     * @use             $this->createException()
     * @use             $this->prepare_where()
     * @use             $this->createQuery()
     * @use             $this->getResult()
     * 
     * @throws          InvalidSortOrderException
     * @throws          InvalidLimitException
     * 
     *
     * @param           mixed           $filter                Multi dimensional array
     * @param           array           $sortorder              Array
     *                                                              'column'    => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     * @param           string           $query_str             If a custom query string needs to be defined.
     *
     * @return          array           $response
     */
    public function listPhoneNumbers($filter = null, $sortorder = null, $limit = null, $query_str = null) {
        $this->resetResponse();
        if (!is_array($sortorder) && !is_null($sortorder)) {
            return $this->createException('InvalidSortOrderException', '', 'err.invalid.parameter.sortorder');
        }

        /**
         * Add filter check to below to set join_needed to true
         */
        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';


        /**
         * Start creating the query
         *
         * Note that if no custom select query is provided we will use the below query as a start
         */
        $localizable = false;
        if (is_null($query_str)) {
            if ($localizable) {
                $query_str = 'SELECT ' . $this->entity['table_localization']['alias']
                        . ' FROM ' . $this->entity['table_localization']['name'] . ' ' . $this->entity['table_localization']['alias']
                        . ' JOIN ' . $this->entity['table_localization']['alias'] . '.COLUMN ' . $this->entity['table']['alias'];
            } else {
                $query_str = 'SELECT ' . $this->entity['phone_number']['alias']
                        . ' FROM ' . $this->entity['phone_number']['name'] . ' ' . $this->entity['phone_number']['alias'];
            }
        }
        /*
         * Prepare ORDER BY section of query
         */
        if (!is_null($sortorder)) {
            foreach ($sortorder as $column => $direction) {
                switch ($column) {
                    case 'id':
                    case 'name':
                    case 'url_key':
                        break;
                }
                $order_str .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY ' . $order_str . ' ';
        }

        /*
         * Prepare WHERE section of query
         */

        if (!is_null($filter)) {
            $filter_str = $this->prepare_where($filter);
            $where_str = ' WHERE ' . $filter_str;
        }



        $query_str .= $where_str . $group_str . $order_str;


        $query = $this->em->createQuery($query_str);

        /*
         * Prepare LIMIT section of query
         */

        if (!is_null($limit) && is_numeric($limit)) {
            /*
             * if limit is set
             */
            if (isset($limit['start']) && isset($limit['count'])) {
                $query = $this->addLimit($query, $limit);
            } else {
                $this->createException('InvalidLimitException', '', 'err.invalid.limit');
            }
        }
        //print_r($query->getSql()); die;
        /*
         * Prepare and Return Response
         */

        $files = $query->getResult();


        $total_rows = count($files);
        if ($total_rows < 1) {
            $this->response['error'] = true;
            $this->response['code'] = 'err.db.entry.notexist';
            return $this->response;
        }

        $this->response = array(
	    'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $files,
                'total_rows' => $total_rows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );

        return $this->response;
    }

    /**
     * @name 		getPhoneNumber()
     * Returns details of a gallery.
     *
     * @since		1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     * @use             $this->listPhoneNumbers()
     *
     * @param           mixed           $phoneNumber               id, url_key
     * @param           string          $by                 entity, id, url_key
     *
     * @return          mixed           $response
     */
    public function getPhoneNumber($phoneNumber, $by = 'id') {
        $this->resetResponse();
        $by_opts = array('id', 'url_key');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if (!is_object($phoneNumber) && !is_numeric($phoneNumber)) {
            return $this->createException('InvalidParameterException', 'PhoneNumber', 'err.invalid.parameter');
        }
        if (is_object($phoneNumber) && $phoneNumber instanceof BundleEntity\PhoneNumber) {
            $phoneNumber = $phoneNumber->getId();
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['phone_number']['alias'] . '.id' , 'comparison' => '=', 'value' => $phoneNumber),
                )
            )
        );

        $response = $this->listPhoneNumbers($filter, null, array('start' => 0, 'count' => 1));
        if ($response['error']) {
            return $response;
        }
        $collection = $response['result']['set'];
        unset($response);
        /**
         * Prepare & Return Response
         */
        $this->response = array(
	    'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }
    /**
     * @name 		    getPhoneNumberByNumber()
     *                  Gets the phone number details with supplied area, country code, number and extension combination.
     *
     * @since		    1.0.3
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->listPhoneNumbers()
     *
     * @param           integer         $countryCode
     * @param           integer         $areaCode
     * @param           integer         $number
     * @param           mixed           $extension
     *
     * @return          mixed           $response
     */
    public function getPhoneNumberByNumber($countryCode, $areaCode, $number, $extension = null) {
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['phone_number']['alias'] . '.country_code' , 'comparison' => '=', 'value' => $countryCode),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['phone_number']['alias'] . '.area_code' , 'comparison' => '=', 'value' => $areaCode),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['phone_number']['alias'] . '.number' , 'comparison' => '=', 'value' => $number),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['phone_number']['alias'] . '.extension' , 'comparison' => is_null($extension) ? 'isnull' : '=', 'value' => $extension),
                ),

            )
        );

        $response = $this->listPhoneNumbers($filter, null, array('start' => 0, 'count' => 1));
        if ($response['error']) {
            return $response;
        }
        $collection = $response['result']['set'];
        unset($response);
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

     /**
     * @name 		insertPhoneNumber()
     * Inserts one or more item into database.
     *
     * @since		1.0.1
     * @version         1.0.3
     * @author          Said İmamoğlu
     *
     * @use             $this->insertFiles()
     *
     * @param           array           $item        Collection of entities or post data.
     *
     * @return          array           $response
     */
    
    public function insertPhoneNumber($item,$by = 'post') {
        $this->resetResponse();
        return $this->insertPhoneNumbers($item);
    }
    /**
     * @name            insertPhoneNumbers()
     * Inserts one or more phone numbers into database.
     *
     * @since           1.0.1
     * @version         1.0.3
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @throws          InvalidParameterException
     * @throws          InvalidMethodException
     *
     * @param           array           $collection        Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertPhoneNumbers($collection) {
        $this->resetResponse();
        /*** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countInserts = 0;
        $insertedItems = array();
        foreach ($collection as $collection) {
            if ($collection instanceof BundleEntity\PhoneNumber) {
                $entity = $collection;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else if (is_object($collection)) {
                $entity = new BundleEntity\PhoneNumber();
                if (isset($collection->id)) {
                    unset($collection->id);
                }
                if (!property_exists($collection, 'date_added')) {
                    $collection->date_added = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                if (!property_exists($collection, 'date_updated')) {
                    $collection->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                foreach ($collection as $column => $value) {
                    $set = 'set' . $this->translateColumnName($column);
                    $entity->$set($value);
                }
                $this->em->persist($entity);
                $insertedItems[] = $entity;

                $countInserts++;
            } else {
                new CoreExceptions\InvalidDataException($this->kernel);
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
        }
        /***
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => $entity->getId(),
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        return $this->response;
    }
    /**
     * @name            updatePhoneNumber()
     * Updates single item. The item must be either a post data (array) or an entity
     * 
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     * 
     * @use             $this->resetResponse()
     * @use             $this->updatePhoneNumbers()
     * 
     * @param           mixed   $phone     Entity or Entity id of a folder
     * 
     * @return          array   $response
     * 
     */

    public function updatePhoneNumber($phone) {
        $this->resetResponse();
        return $this->updatePhoneNumbers(array($phone));
    }

    /**
     * @name            updateEmailAddresses()
     * Updates one or more item details in database.
     * 
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     * 
     * @use             $this->update_entities()
     * @use             $this->createException()
     * @use             $this->listPhoneNumbers()
     * 
     * 
     * @throws          InvalidParameterException
     * 
     * @param           array   $collection     Collection of item's entities or array of entity details.
     * 
     * @return          array   $response
     * 
     */

    public function updatePhoneNumbers($collection) {

        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countUpdates = 0;
        $updatedItems = array();
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
                    $data->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                if (property_exists($data, 'date_added')) {
                    unset($data->date_added);
                }
                $response = $this->getPhoneNumber($data->id, 'id');
                if ($response['error']) {
                    return $this->createException('EntityDoesNotExist', 'Volume Pricing with id ' . $data->id, 'err.invalid.entity');
                }
                $oldEntity = $response['result']['set'];
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
            } else {
                new CoreExceptions\InvalidDataException($this->kernel);
            }
        }
        if ($countUpdates > 0) {
            $this->em->flush();
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $updatedItems,
                'total_rows' => $countUpdates,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.update.done',
        );
        return $this->response;
    }
   
    /*
     * @name            listEmailAddressesOfMember()
     * Lists email addresses of a given member
     * 
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     * 
     * @use             $this->type_opts
     * @use             $this->listEmailAddresses()
     * 
     * @param           char    $type        Code of type (image use i,for audio use a)
     * 
     * @return          array   $response
     * 
     */

    public function listEmailAddressesOfMember($member, $sortorder = null, $limit = null, $query_str = null) {
        $filter[] = array(
            'glue' => ' and',
            'condition' => array(
                'column' => $this->entity['email_address']['alias'] . '.member',
                'comparison' => '=',
                'value' => $member)
        );
        return $this->listEmailAddresses($filter, $sortorder, $limit, $query_str);
    }
   
    /**
     * @name            listPhoneNumbersOfMembers()
     * Lists phone numbersof a given member
     * 
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     * 
     * @use             $this->type_opts
     * @use             $this->listPhoneNumbers()
     * 
     * @param           char    $member        Code of type (image use i,for audio use a)
     * 
     * @return          array   $response
     *
     * * @deprecated      Will be deleted in v1.3.0. Use initDefaults instead.
     * 
     */

    public function listPhoneNumbersOfMembers($member, $sortorder = null, $limit = null, $query_str = null) {
        $filter[] = array(
            'glue' => ' and',
            'condition' => array(
                'column' => $this->entity['phone_number']['alias'] . '.member',
                'comparison' => '=',
                'value' => $member)
        );
        return $this->listPhoneNumbers($filter, $sortorder, $limit, $query_str);
    }



    /**
     * @name            listPhoneNumbersOfMember()
     *                  Lists records from phone_numbers_of_member
     *
     * @since           1.0.2
     * @version         1.0.2
     *
     * @author          Said Imamoglu
     *
     * @param           mixed       $filter
     * @param           array       $sortOrder
     * @param           array       $limit
     * @param           string      $queryStr
     *
     * @return          array       $response
     *
     */

    public function listPhoneNumbersOfMember($filter,$sortOrder = null, $limit = null, $queryStr = null) {
        $this->resetResponse();
        if (!is_array($sortOrder) && !is_null($sortOrder)) {
            return $this->createException('InvalidSortOrder', '', 'err.invalid.parameter.sortorder');
        }
        /**
         * Add filter checks to below to set join_needed to true.
         */
        /**         * ************************************************** */
        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';

        /**
         * Start creating the query.
         *
         * Note that if no custom select query is provided we will use the below query as a start.
         */
        if (is_null($queryStr)) {
            $queryStr = 'SELECT ' . $this->entity['phone_numbers_of_member']['alias']
                . ' FROM ' . $this->entity['phone_numbers_of_member']['name'] . ' ' . $this->entity['phone_numbers_of_member']['alias'];
        }

        /**
         * Prepare WHERE section of query.
         */
        if ($filter != null) {
            $filter_str = $this->prepareWhere($filter);
            $where_str .= ' WHERE ' . $filter_str;
        }
        $queryStr .= $where_str . $group_str . $order_str;
        $query = $this->em->createQuery($queryStr);

        /**
         * Prepare & Return Response
         */
        $result = $query->getResult();
        $totalRows = count($result);
        if ($totalRows < 1) {
            $this->response['code'] = 'err.db.entry.notexist';
            return $this->response;
        }
        $phoneNumbers = array();
        foreach ($result as $record) {
            $phoneNumbers[] = $record->getPhoneNumber();
        }
        unset($result);

        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $phoneNumbers,
                'total_rows' => $totalRows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }
    /**
     * @name            listPhoneNumbersOfMemberByMember()
     *                  Lists phone numbers of given member
     *
     * @since           1.0.2
     * @version         1.0.2
     *
     * @author          Said Imamoglu
     *
     * @param           mixed       $member
     * @param           mixed       $filter
     * @param           array       $sortOrder
     * @param           array       $limit
     * @param           string      $queryStr
     *
     * @return          array       $response
     *
     */

    public function listPhoneNumbersOfMemberByMember($member,$filter = array(),$sortOrder = null, $limit = null, $queryStr = null) {
        if (!is_numeric($member) && !$member instanceof MMBEntity\Member && !$member instanceof \stdClass) {
            return $this->createException('InvalidParameter', '$member parameter must hold BiberLtd\\Core\\Bundles\\MemberManagementBundle\\Entity\\Member Entity, string representing url_key or sku, or integer representing database row id', 'msg.error.invalid.parameter.member');
        }
        if ($member instanceof MMBEntity\Member) {
            $member = $member;
        }
        if ($member instanceof \stdClass) {
            $member = (int) $member->id;
        }
        $memberModel = $this->kernel->getContainer()->get('membermanagement.model');
        if (is_numeric($member)) {
            $response = $memberModel->getMember($member, 'id');
            if ($response['error']) {
                return $this->createException('EntityDoesNotExist', 'Table: member, id: ' . $member, 'msg.error.db.member.notfound');
            }
            $member = $response['result']['set'];
        } else if (is_string($member)) {
            $response = $memberModel->getMember($member, 'sku');
            if ($response['error']) {
                $response = $memberModel->getMember($member, 'url_key');
                if ($response['error']) {
                    return $this->createException('EntityDoesNotExist', 'Table : member, id / sku / url_key: ' . $member, 'msg.error.db.member.notfound');
                }
            }
            $member = $response['result']['set'];
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['phone_numbers_of_member']['alias'] . '.member', 'comparison' => '=', 'value' => $member->getId()),
                )
            )
        );
        return $this->listPhoneNumbersOfMember($filter,$sortOrder,$limit,$queryStr);
    }
    /**
     * @name            listPhoneNumbersOfMemberByMember()
     *                  Lists phone numbers of given member
     *
     * @since           1.0.2
     * @version         1.0.2
     *
     * @author          Said Imamoglu
     *
     * @param           mixed       $member
     * @param           mixed       $type
     *
     * @return          array       $response
     *
     */

    public function getPhoneNumberOfMemberByType($member,$type) {
        if ((!is_int($member) && !$member instanceof \stdClass && !$member instanceof MMBEntity\Member) || (!is_string($type)) && !in_array($type,array('h','m','f'))) {
            return $this->createException('InvalidParameter', '', 'err.invalid.parameter.address');
        }

        if ($member instanceof MMBEntity\Member) {
            $member = $member->getId();
        }
        if ($member instanceof \stdClass) {
            $member = $member->id;
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['phone_numbers_of_member']['alias'] . '.member', 'comparison' => '=', 'value' => $member),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['phone_numbers_of_member']['alias'] . '.type', 'comparison' => '=', 'value' => $type),
                ),
            )
        );
        $response = $this->listPhoneNumbersOfMember($filter);
        if ($response['error']) {
            return $response;
        }
        $response['result']['set'] = $response['result']['set'][0];
        $response['result']['total_rows'] = 1;
        return $response;
    }

    /**
     * @name            isPhoneNumberAssociatedWithMember ()
     *                  Checks if the phone number is already associated with the member
     *
     * @since           1.0.3
     * @version         1.0.3
     * @author          Said İmamoğlu
     *
     * @user            $this->createException
     *
     * @param           int $phone
     * @param           int $member
     * @param           bool $byPass
     *
     * @return          mixed           bool or $response
     */
    public function isPhoneNumberAssociatedWithMember($phone, $member, $byPass= false)
    {
        return $this->arePhoneNumbersAssociatedWithMember(array($phone),$member,$byPass);
    }

    /***
     * @name            addPhoneNumbersToMember()
     *                  Associates member and member with type.
     *
     * @since           1.0.4
     * @version         1.0.4
     * @author          Said Imamoglu
     *
     * @use             $this->createException()
     *
     *
     * @throws          InvalidParameterException
     *
     * @param   array           $phones
     * @param   mixed           $member
     *
     * @return          array   $response
     *
     */

    public function addPhoneNumbersToMember($phones,$member) {
        $this->resetResponse();
        /**
         * Validate Parameters
         */
        foreach ($phones as $key=>$phone) {
            if (!is_numeric($phone) && !is_string($phone) && !$phone instanceof BundleEntity\PhoneNumber) {
                return $this->createException('InvalidParameter', 'PhoneNumber', 'error.invalid.phone.parameter');
            }
        }

        if (!is_numeric($member) && !is_string($member) && !$member instanceof MMBEntity\Member) {
            return $this->createException('InvalidParameter', 'Member', 'error.invalid.member.parameter');
        }
        $phoneCollectionForList = array();
        foreach ($phones as $phone) {
            $phoneCollectionForList[] = $phone->getId();
        }


        /** If no entity is provided as address we need to check if it does exist */
        $filter = array();
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' =>$this->getEntityDefinition('phone_number','alias').'.id' , 'comparison' => 'in', 'value' =>$phoneCollectionForList ),
                )
            )
        );
        unset($phoneCollectionForList);
        $response = $this->listPhoneNumbers($filter);
        $phoneCollection = array();
        if (!$response['error']) {
            $phoneCollection = $response['result']['set'];
        }

        $count = 0;
        /** Get Member */
        $memberModel = $this->kernel->getContainer()->get('membermanagement.model');
        if (is_int($member)) {
            $response = $memberModel->getMember($member);
            if ($response['error']) {
                return $this->createException('EntityDoesNotExist', 'Member', 'error.db.member.notexist');
            }
            $member = $response['result']['set'];
            unset($response);
        }

        /** Check if association exists */
        if ($this->arePhoneNumbersAssociatedWithMember($phoneCollection,$member,true)) {
            return $this->createException('EntityAlreadyExistException', 'Member', 'error.db.phonenumberofmember.exist');
        }

        /** prepare object */
        foreach ($phoneCollection as $phone) {
            $aom = new BundleEntity\PhoneNumbersOfMember();
            $now = new \DateTime('now', new \DateTimezone($this->kernel->getContainer()->getParameter('app_timezone')));
            $aom->setMember($member);
            $aom->setPhoneNumber($phone);;
            $aom->setDateAdded($now);
            $aom->setDateUpdated($now);
            /** persist entry */
            $this->em->persist($aom);
            $collection[] = $aom;
            $count++;
        }
        /** flush all into database */
        if ($count > 0) {
            $this->em->flush();
        } else {
            $this->response['code'] = 'error.db.insert.failed';
        }

        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection,
                'total_rows' => $count,
                'last_insert_id' => -1,
            ),
            'error' => false,
            'code' => 'success.db.insert.done',
        );
        unset($count, $collection);
        return $this->response;
    }
    /**
     * @name            arePhoneNumbersAssociatedWithMember ()
     *                  Checks if the phone numbers are already associated with the member
     *
     * @since           1.0.3
     * @version         1.0.3
     * @author          Said İmamoğlu
     *
     * @user            $this->createException
     *
     * @param           mixed $phones
     * @param           int $member
     * @param           bool $byPass
     *
     * @return          mixed           bool or $response
     */
    public function arePhoneNumbersAssociatedWithMember($phones, $member, $byPass= false)
    {
        $this->resetResponse();
        /**
         * Validate Parameters
         */
        if (!is_array($phones)) {
            return $this->createException('InvalidParameter', 'PhoneNumbers Collection', 'err.invalid.parameter.phone.numbers.collection');
        }

        if (!is_numeric($member) && !$member instanceof MMBEntity\Member && !$member instanceof \stdClass) {
            return $this->createException('InvalidParameter', 'Member', 'err.invalid.parameter.product_category');
        }
        if ($member instanceof \stdClass) {
            $member = $member->id;
        }
        if ($member instanceof MMBEntity\Member) {
            $member = $member->getId();
        }
        $phoneCollection = array();
        foreach ($phones as $phone) {
            if (!is_numeric($phone)  && !$phone instanceof BundleEntity\PhoneNumber && !$phone instanceof \stdClass) {
                return $this->createException('InvalidParameter', 'PhoneNumber', 'err.invalid.parameter.product');
            }
            if ($phone instanceof \stdClass) {
                $phone = $phone->id;
            }
            if ($phone instanceof BundleEntity\PhoneNumber) {
                $phone = $phone->getId();
            }
            $phoneCollection[] = $phone;
        }

        $filter = array();
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['phone_numbers_of_member']['alias'].'.member', 'comparison' => '=', 'value' => $member),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['phone_numbers_of_member']['alias'].'.phone_number', 'comparison' =>'in','value'=> $phoneCollection),
                ),
            )
        );
        $found = true;
        $code = 'success.db.entry.exist';
        $response = $this->listPhoneNumbersOfMember($filter);
        if ($response['error']) {
            $found = false;
            $code = 'error.db.entry.notexist';
        }
        if ($byPass) {
            return $found;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $response['result']['set'],
                'total_rows' => $response['result']['total_rows'],
                'last_insert_id' => null,
            ),
            'error' => $found == true ? false : true,
            'code' => $code,
        );
        return $this->response;
    }

}

/**
 * Change Log
 * **************************************
 * v1.0.4                      Said İmamoğlu
 * 27.06.2013
 * **************************************
 * A addPhoneNumbersToMember()
 * **************************************
 * v1.0.3                      Said İmamoğlu
 * 06.06.2013
 * **************************************
 * A isPhoneNumberAssociatedWithMember()
 * A arePhoneNumbersAssociatedWithMember()
 * **************************************
 * v1.0.2                      Said İmamoğlu
 * 05.06.2013
 * **************************************
 * A listPhoneNumbersOfMember()
 * A listPhoneNumbersOfMemberByMember()
 * **************************************
 * v1.0.1                      Said İmamoğlu
 * 04.12.2013
 * **************************************
 * A deleteContactInformationType()
 * A deleteContactInformationTypes()
 * A deleteEmailAddress()
 * A deleteEmailAddress()
 * A deletePhoneNumber()
 * A deletePhoneNumbers()
 * A doesContactInformationTypeExist()
 * A doesEmailAddressExist()
 * A getContactInformationType()
 * A listContactInformationTypes()
 * A getEmailAddress()
 * A listEmailAddresses()
 * A getPhoneNumber()
 * A listPhoneNumbers()
 * A insertContactInformationType()
 * A insertContactInformationTypes()
 * A insertEmailAddress()
 * A insertEmailAddresses()
 * A insertPhoneNumber()
 * A insertPhoneNumbers()
 * A updateContactInformationType()
 * A updateContactInformationType()
 * A updateEmailAddress()
 * A updateEmailAddresses()
 * A updatePhoneNumber()
 * A updatePhoneNumbers()
 * A listEmailAddressesOfMember()
 * A listPhoneNumbersOfMembers()
 * 
 * **************************************
 * v1.0.0                      Said İmamoğlu
 * 03.12.2013
 * **************************************
 * A __construct()
 * A __destruct()
 */