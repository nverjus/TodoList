<?php
namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private $client;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function tearD()
    {
        $this->client = null;
    }

    public function testLoginWithValidCredentials()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'admin';

        $crawler = $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('a[href="/logout"]')->count());
    }

    public function testLoginWithInvalidCredentials()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'wrongPassword';

        $crawler = $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('html:contains("Invalid credentials.")')->count());
    }

    public function testLogout()
    {
        $crawler = $this->client->request('GET', '/', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));

        $link = $crawler->filter('a[href="/logout"]')->link();
        $crawler = $this->client->click($link);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }
}
