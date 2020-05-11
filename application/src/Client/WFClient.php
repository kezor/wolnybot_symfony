<?php declare(strict_types=1);


namespace App\Client;


use App\Entity\Building;
use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class WFClient
{

    /**
     * @var Player
     */
    private $player;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(Player $player, UrlGenerator $urlGenerator, EntityManagerInterface $entityManager)
    {
        $this->urlGenerator = $urlGenerator;
        $this->player = $player;
        $this->entityManager = $entityManager;
        $this->login();
    }

    public function getDashboardData()
    {
        return $this->callGet($this->urlGenerator->getFarmUrl($this->player));
    }

    public function cropFarmland(Building $farmland)
    {
        return $this->callGet($this->urlGenerator->getCropGardenUrl($farmland));
    }

    private function getClient(): Client
    {
        return $this->client;
    }

    private function login()
    {
        $player = $this->player;

        if (!$player->canReuseToken()) {
            $client = $this->relogin($player);
        } else {
            $cookieJar = new CookieJar(false, $player->getCookies());

            $client = new Client([
                'cookies' => $cookieJar,
            ]);
        }

        $this->client = $client;
    }

    private function callGet($uri)
    {
        return $this->getClient()->get($uri)->getBody()->__toString();
    }

    public function relogin(Player $player): Client
    {
        try {
            $client = new Client(['cookies' => true]);

            $res = $client->request('POST', 'https://www.wolnifarmerzy.pl/ajax/createtoken2.php?n=' . time(), [
                'form_params' => [
                    'server'   => $player->getServerId(),
                    'username' => $player->getUsername(),
                    'password' => $player->getPassword(),
                    'ref'      => '',
                    'retid'    => '',
                    '_'        => '',
                ],
            ]);

            $responseBody = $res->getBody()->__toString();

            $matches = null;
            preg_match('^\[1,"[a-z\:]+^', $responseBody, $matches);
            if (empty($matches)) {
                throw new \Exception('Wrong login credentials');
            }

            $url = substr($responseBody, 4, strlen($responseBody) - 6);

            $url = str_replace('\\', '', $url);

            $res = $client->request('GET', $url);

            $body = $res->getBody()->__toString();

            $needle = 'var rid = \'';
            $startPos = strpos($body, $needle) + strlen($needle);

            $body = substr($body, $startPos);

            $length = strpos($body, '\'');

            $token = substr($body, 0, $length);

            if (empty($token)) {
                throw new \Exception('Token is invalid');
            }
            $player->setToken($token);
            $player->setCookies($client->getConfig('cookies')->toArray());

            $this->entityManager->flush();
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }

        return $client;
    }

    public function getFarmlandFields(Building $building)
    {
        $url = $this->urlGenerator->getGardenInitUrl($building, $this->player);

        return $this->callGet($url);
    }
}