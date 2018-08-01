<?php
namespace  Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserControllerTest extends WebTestCase
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

    public function testUserListPageIsUp()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/users');
        self::assertEquals(1, $crawler->filter('html:contains("Nom d\'utilisateur")')->count());
    }

    public function testUsersAtionsAreUnreachableForAnonymousUser()
    {
        $crawler = $this->client->request('GET', '/users');
        $crawler = $this->client->followRedirect();
        self::assertEquals(1, $crawler->filter('form[action="/login_check"]')->count());

        $crawler = $this->client->request('GET', '/users/create');
        $crawler = $this->client->followRedirect();
        self::assertEquals(1, $crawler->filter('form[action="/login_check"]')->count());

        $crawler = $this->client->request('GET', '/users/1/edit');
        $crawler = $this->client->followRedirect();
        self::assertEquals(1, $crawler->filter('form[action="/login_check"]')->count());
    }

    public function testUsersAtionsAreUnreachableForNonAdminUser()
    {
        $crawler = $this->client->request('GET', '/users', array(), array(), array(
          'PHP_AUTH_USER' => 'user1',
          'PHP_AUTH_PW'   => 'user1',
        ));
        self::assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->request('GET', '/users/create', array(), array(), array(
          'PHP_AUTH_USER' => 'user1',
          'PHP_AUTH_PW'   => 'user1',
        ));
        self::assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->request('GET', '/users/1/edit', array(), array(), array(
          'PHP_AUTH_USER' => 'user1',
          'PHP_AUTH_PW'   => 'user1',
        ));
        self::assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testUserCreateWithMissingFields()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/users/create');

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['user[username]'] = '';
        $form['user[password][first]'] = '';
        $form['user[password][second]'] = '';
        $form['user[email]'] = '';

        $crawler = $this->client->submit($form);

        self::assertEquals(1, $crawler->filter('html:contains("Vous devez saisir un nom d\'utilisateur.")')->count());
        self::assertEquals(1, $crawler->filter('html:contains("Vous devez saisir une adresse email.")')->count());
    }

    public function testUserCreateWithInvalidEmail()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/users/create');

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['user[username]'] = 'User Test';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test';
        $form['user[email]'] = 'lalalala';

        $crawler = $this->client->submit($form);

        self::assertEquals(1, $crawler->filter('html:contains("Le format de l\'adresse n\'est pas correcte.")')->count());
    }

    public function testUserCreateWithNotMatchingPassword()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/users/create');

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['user[username]'] = 'User Test';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test2';
        $form['user[email]'] = 'test@mail.com';

        $crawler = $this->client->submit($form);

        self::assertEquals(1, $crawler->filter('html:contains(" Les deux mots de passe doivent correspondre.")')->count());
    }

    public function testUserCreateWithAlreadyUsedMail()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/users/create');

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['user[username]'] = 'User Test';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test';
        $form['user[email]'] = 'user1@mail.com';

        $crawler = $this->client->submit($form);

        self::assertEquals(1, $crawler->filter('html:contains("This value is already used.")')->count());
    }

    public function testUserCreateWithGoodValue()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/users/create');

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['user[username]'] = 'User Test';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test';
        $form['user[email]'] = 'test@mail.com';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        self::assertEquals(1, $crawler->filter('html:contains("User Test")')->count());
        self::assertEquals(1, $crawler->filter('html:contains("L\'utilisateur a bien été ajouté.")')->count());
    }

    public function testUserEditWithMissingFields()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/users/2/edit');

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['user[username]'] = '';
        $form['user[password][first]'] = '';
        $form['user[password][second]'] = '';
        $form['user[email]'] = '';

        $crawler = $this->client->submit($form);

        self::assertEquals(1, $crawler->filter('html:contains("Vous devez saisir un nom d\'utilisateur.")')->count());
        self::assertEquals(1, $crawler->filter('html:contains("Vous devez saisir une adresse email.")')->count());
    }

    public function testUserEditWithInvalidEmail()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/users/2/edit');

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['user[username]'] = 'User Test';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test';
        $form['user[email]'] = 'lalalala';

        $crawler = $this->client->submit($form);

        self::assertEquals(1, $crawler->filter('html:contains("Le format de l\'adresse n\'est pas correcte.")')->count());
    }

    public function testUserEditWithNotMatchingPassword()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/users/2/edit');

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['user[username]'] = 'User Test';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test2';
        $form['user[email]'] = 'test2@mail.com';

        $crawler = $this->client->submit($form);

        self::assertEquals(1, $crawler->filter('html:contains(" Les deux mots de passe doivent correspondre.")')->count());
    }

    public function testUserEditWithGoodValue()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/users/3/edit');

        $form = $crawler->filter('button[type="submit"]')->form();

        $form['user[username]'] = 'User Edited';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test';
        $form['user[email]'] = 'test2@mail.com';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        self::assertEquals(1, $crawler->filter('html:contains("User Edited")')->count());
        self::assertEquals(1, $crawler->filter('html:contains("L\'utilisateur a bien été modifié")')->count());
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
