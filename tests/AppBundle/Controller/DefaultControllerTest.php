<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultControllerTest extends WebTestCase
{
    private $client;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testHomepasWithAnonymousUser()
    {
        $crawler = $this->client->request('GET', '/');
        static::assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        static::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('form[action="/login_check"]')->count());
    }

    public function testHomepageWithAuthentifiedUser()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/');
        static::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertGreaterThan(0, $crawler->filter('html:contains("Bienvenue sur Todo List")')->count());
    }

    public function tearDown()
    {
        $this->client = null;
    }

    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');

        $firewallName = 'main';

        $token = new UsernamePasswordToken('admin', 'admin', $firewallName, array('ROLE_ADMIN'));
        $session->set('_security_'.$firewallName, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}
