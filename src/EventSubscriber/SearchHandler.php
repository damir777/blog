<?php

namespace App\EventSubscriber;

use App\Service\TaxonBagService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SearchHandler implements EventSubscriberInterface
{
    private const LIMIT = 5;

    public function __construct(private readonly TaxonBagService $service)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'formatSearchParameters',
        ];
    }

    public function formatSearchParameters(RequestEvent $event): void
    {
        $parameters = $event->getRequest()->query->all();

        $search = [];
        $search['limit'] = self::LIMIT;
        $search['offset'] = 0;

        if ($parameters !== []) {
            foreach ($parameters as $parameter => $value) {
                if ($parameter === 'page') {
                    $page = (int)$value;

                    if ($page < 1) {
                        throw new BadRequestException('Page parameter is not valid');
                    }

                    $search[$parameter] = $page;
                    $search['offset'] = self::LIMIT * ($page - 1);

                    continue;
                }

                if (mb_strpos($parameter, 'taxonomy_') !== false) {
                    $bagName = str_replace('_', '-', mb_substr($parameter, mb_strpos($parameter, '_') + 1));

                    $bag = $this->service->findByName($bagName);
                    $search['content'][$bag->getId()] = $value;

                    continue;
                }

                $search['fields'][$parameter] = $value;
            }
        }

        $event->getRequest()->query->set('search', $search);
    }
}