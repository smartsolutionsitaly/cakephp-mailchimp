<?php
/**
 * cakephp-mailchimp (https://github.com/smartsolutionsitaly/cakephp-mailchimp)
 * Copyright (c) 2018 Smart Solutions S.r.l. (https://smartsolutions.it)
 *
 * MailChimp connector for CakePHP
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @category  cakephp-plugin
 * @package   cakephp-mailchimp
 * @author    Lucio Benini <dev@smartsolutions.it>
 * @copyright 2018 Smart Solutions S.r.l. (https://smartsolutions.it)
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 * @link      https://smartsolutions.it Smart Solutions
 * @since     1.0.0
 */

namespace SmartSolutionsItaly\CakePHP\MailChimp\Http\Client;

use Cake\Core\Configure;
use Cake\Http\Client;
use Cake\Http\Client\Response;
use Cake\Utility\Security;
use Cake\Validation\Validation;
use Exception;

/**
 * MailChimp client.
 * @package SmartSolutionsItaly\CakePHP\MailChimp\Http\Client
 * @author Lucio Benini <dev@smartsolutions.it>
 * @since 1.0.0
 */
class MailChimpClient
{
    /**
     * HTTP client.
     * @var Client
     * @since 1.0.0
     */
    protected $_client;

    /**
     * List ID.
     * @var string
     * @since 1.0.0
     */
    protected $_list;

    /**
     * Constructor.
     * @since 1.0.0
     */
    public function __construct()
    {
        list ($key, $dc) = explode('-', Configure::read('MailChimp.key'));

        $this->_client = new Client([
            'host' => $dc . '.api.mailchimp.com/3.0/',
            'scheme' => 'https',
            'auth' => [
                'username' => 'apikey',
                'password' => $key
            ],
            'type' => 'json'
        ]);
    }

    /**
     * Sets the MailChimp's list from the configuration file.
     * @param string $key The key as setted in the configuration file.
     * @return MailChimpClient The current instance.
     * @since 1.0.0
     */
    public function setListFromKey(string $key): MailChimpClient
    {
        return $this->setList(Configure::read('MailChimp.lists.' . $key));
    }

    /**
     * Gets the status of a subscription.
     * @param string $email The subscriber's e-mail.
     * @return mixed|null Returns the MailChimp's response or a null value.
     * @since 1.0.0
     */
    public function status(string $email)
    {
        if (Validation::email($email) && $this->getList()) {
            try {
                $res = $this->_client->get('lists/' . $this->getList() . '/members/' . Security::hash($email, 'md5'));
            } catch (Exception $ex) {
                return null;
            }

            return static::processResponse($res);
        }

        return null;
    }

    /**
     * Gets the MailChimp's list.
     * @return string The list name.
     * @since 1.0.0
     */
    public function getList()
    {
        return $this->_list;
    }

    /**
     * Sets the MailChimp's list.
     * @param string $name The list name.
     * @return MailChimpClient The current instance.
     * @since 1.0.0
     */
    public function setList(string $name): MailChimpClient
    {
        $this->_list = $name;

        return $this;
    }

    /**
     * Converts a response content to its object representation.
     * @param Response $res The response object to process.
     * @return mixed|null The response content to its object representation or a null value.
     * @since 1.0.0
     */
    protected static function processResponse(Response $res)
    {
        $code = $res->getStatusCode();

        if ($code >= 200 && $code < 300) {
            return json_decode($res->getBody()->getContents());
        } else {
            return null;
        }
    }

    /**
     * Unsubscribe the subscriber with the given e-mail from a list.
     * @param string $email The subscriber's e-mail.
     * @param array $fields The "merge" fields.
     * @param string $language The subscriber's language.
     * @param string $ip The subscriber's IP.
     * @return mixed|null Returns the MailChimp's response or a null value.
     * @since 1.0.0
     */
    public function subscribe(string $email, array $fields = [], $language = null, $ip = null)
    {
        if (Validation::email($email)) {
            $vars = [
                'email_address' => $email,
                'status' => 'subscribed'
            ];

            if (!empty($fields)) {
                $vars['merge_fields'] = $fields;
            }

            if (!$language) {
                $language = Configure::read('App.defaultLocale');
            }

            if ($language) {
                $language = explode('_', $language, 2);

                if (!empty($language[0])) {
                    $vars['language'] = $language[0];
                }
            }

            if ($ip && Validation::ip($ip)) {
                $vars['ip_signup'] = $ip;
                $vars['ip_opt'] = $ip;
            }

            try {
                $res = $this->_client->post('lists/' . $this->getList() . '/members/', json_encode($vars));
            } catch (Exception $ex) {
                return null;
            }

            return static::processResponse($res);
        }

        return null;
    }

    /**
     * Unsubscribes the subscriber with the given e-mail from a list.
     * @param string $email The subscriber's e-mail.
     * @return mixed|null Returns the MailChimp's response or a null value.
     * @since 1.0.0
     */
    public function unsubscribe(string $email)
    {
        if (Validation::email($email)) {
            try {
                $res = $this->_client->patch('lists/' . $this->getList() . '/members/' . Security::hash($email, 'md5'), json_encode([
                    'status' => 'unsubscribed'
                ]));
            } catch (Exception $ex) {
                return null;
            }

            return static::processResponse($res);
        }

        return null;
    }

    /**
     * Deletes the subscriber with the given e-mail from a list.
     * @param string $email The subscriber's e-mail.
     * @return mixed|null Returns the MailChimp's response or a null value.
     * @since 1.0.0
     */
    public function delete(string $email)
    {
        if (Validation::email($email)) {
            try {
                $res = $this->_client->delete('lists/' . $this->getList() . '/members/' . Security::hash($email, 'md5'));
            } catch (Exception $ex) {
                return null;
            }

            return static::processResponse($res);
        }

        return null;
    }
}
