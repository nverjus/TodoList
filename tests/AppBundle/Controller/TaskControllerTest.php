<?php
namespace  Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TaskControllerTest extends WebTestCase
{
    private $client;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function tearDown()
    {
        $this->client = null;
    }

    public function testTaskListPageIsUp()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/tasks');
        static::assertEquals(1, $crawler->filter('a[href="/tasks/create"]')->count());
    }

    public function testTaskAtionsAreUnreachableForAnonymousUser()
    {
        $crawler = $this->client->request('GET', '/tasks');
        $crawler = $this->client->followRedirect();
        static::assertEquals(1, $crawler->filter('form[action="/login_check"]')->count());

        $crawler = $this->client->request('GET', '/tasks/1/toggle');
        $crawler = $this->client->followRedirect();
        static::assertEquals(1, $crawler->filter('form[action="/login_check"]')->count());

        $crawler = $this->client->request('GET', '/tasks/create');
        $crawler = $this->client->followRedirect();
        static::assertEquals(1, $crawler->filter('form[action="/login_check"]')->count());

        $crawler = $this->client->request('GET', '/tasks/1/edit');
        $crawler = $this->client->followRedirect();
        static::assertEquals(1, $crawler->filter('form[action="/login_check"]')->count());

        $crawler = $this->client->request('GET', '/tasks/1/delete');
        $crawler = $this->client->followRedirect();
        static::assertEquals(1, $crawler->filter('form[action="/login_check"]')->count());
    }

    public function testTaskCreateWithMissingFields()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/tasks/create');

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['task[title]'] = '';
        $form['task[content]'] = '';

        $crawler = $this->client->submit($form);

        static::assertEquals(1, $crawler->filter('html:contains("Vous devez saisir un titre.")')->count());
        static::assertEquals(1, $crawler->filter('html:contains("Vous devez saisir du contenu.")')->count());
    }

    public function testTaskCreateWithGoodValues()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/tasks/create');

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['task[title]'] = 'test test';
        $form['task[content]'] = 'test test';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        static::assertEquals(1, $crawler->filter('html:contains("La tâche a été bien été ajoutée.")')->count());
    }

    public function testTaskEditWithMissingFields()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/tasks/2/edit');

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['task[title]'] = '';
        $form['task[content]'] = '';

        $crawler = $this->client->submit($form);

        static::assertEquals(1, $crawler->filter('html:contains("Vous devez saisir un titre.")')->count());
        static::assertEquals(1, $crawler->filter('html:contains("Vous devez saisir du contenu.")')->count());
    }

    public function testTaskEditWithGoodValues()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/tasks/2/edit');

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['task[title]'] = 'test test';
        $form['task[content]'] = 'test test';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        static::assertEquals(1, $crawler->filter('html:contains("La tâche a bien été modifiée.")')->count());
        static::assertEquals(1, $crawler->filter('html:contains("test test")')->count());
    }

    public function testTaskToogleToIsDone()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/tasks');

        $form = $crawler->filter('button[class="btn btn-success btn-sm pull-right"]')->first()->form();
        $crawler = $this->client->submit($form);
        static::assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        static::assertEquals(1, $crawler->filter('html:contains("La tâche First task of admin a bien été marquée comme faite.")')->count());
    }

    public function testTaskToogleToIsNotDone()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/tasks');

        $form = $crawler->filter('button[class="btn btn-success btn-sm pull-right"]')->first()->form();
        $crawler = $this->client->submit($form);
        static::assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        static::assertEquals(1, $crawler->filter('html:contains("La tâche First task of admin a bien été marquée comme non terminée.")')->count());
    }

    public function testDeleteAnOwnedTask()
    {
        $crawler = $this->client->request('GET', '/tasks/1/delete', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));

        $crawler = $this->client->followRedirect();
        static::assertEquals(0, $crawler->filter('html:contains("First task of admin")')->count());
    }

    public function testDeleteANotOwnedTask()
    {
        $crawler = $this->client->request('GET', '/tasks/2/delete', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));

        static::assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminDeleteAnAnonymousTask()
    {
        $crawler = $this->client->request('GET', '/tasks/4/delete', array(), array(), array(
          'PHP_AUTH_USER' => 'admin',
          'PHP_AUTH_PW'   => 'admin',
        ));

        $crawler = $this->client->followRedirect();
        static::assertEquals(0, $crawler->filter('html:contains("First task of anonymous user")')->count());
    }

    public function testUserDeleteAnAnonymousTask()
    {
        $crawler = $this->client->request('GET', '/tasks/3/delete', array(), array(), array(
          'PHP_AUTH_USER' => 'user1',
          'PHP_AUTH_PW'   => 'user1',
        ));
        static::assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
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
