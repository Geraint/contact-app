<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain;
use App\Repository;
use Doctrine\DBAL;
use Doctrine\ORM;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Slim\Flash\Messages;
use Slim\Psr7\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig;
use UnexpectedValueException;

class ContactController
{
    public function __construct(
        private Twig\Environment $twig,
        private ORM\EntityManager $entityManager,
        private ValidatorInterface $validator,
        private Messages $flash
    ) {
    }

    /**
     * @throws Twig\Error\Error
     * @throws RuntimeException
     */
    public function list(
        Request $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $search = $request->getQueryParams()['q'] ?? '';
        assert(is_string($search));
        $contacts = empty($search)
            ? $this->getRepository()->findAll()
            : $this->getRepository()->search($search);

        $response->getBody()->write(
            $this->twig->render(
                'contact\index.html.twig',
                [
                    'search'   => $search,
                    'contacts' => $contacts,
                    'messages' => $this->flash->getMessages(),
                ]
            )
        );
        return $response;
    }

    private function getRepository(): Repository\ContactRepository
    {
        return $this->entityManager->getRepository(Domain\Contact::class);
    }

    /**
     * @throws RuntimeException
     * @throws Twig\Error\Error
     */
    public function new(
        Request $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $response->getBody()->write(
            $this->twig->render(
                'contact\new.html.twig',
                [
                    'contact'  => new Domain\Contact(),
                    'messages' => $this->flash->getMessages(),
                ]
            )
        );
        return $response;
    }

    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws Twig\Error\Error
     */
    public function create(
        Request $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $form = (array) $request->getParsedBody();

        $contact = new Domain\Contact();
        $contact->setFirst($form['first-name']);
        $contact->setLast($form['last-name']);
        $contact->setEmail($form['email']);
        $contact->setPhone($form['phone']);

        try {
            $violations = $this->validator->validate($contact);
            $errors = [];
            if (count($violations) > 0) {
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                throw new UnexpectedValueException('The form has failed validation');
            }

            $this->entityManager->persist($contact);
            $this->entityManager->flush();
            $this->flash->addMessage('info', 'Created new contact!');
            return $response
                ->withHeader('Location', '/contacts')
                ->withStatus(302);
        } catch (
            ORM\Exception\ORMException |
            ORM\ORMInvalidArgumentException |
            DBAL\Exception\UniqueConstraintViolationException |
            UnexpectedValueException $e
        ) {
            $this->flash->addMessageNow('danger', $e->getMessage());
            $response->getBody()->write(
                $this->twig->render(
                    'contact\new.html.twig',
                    [
                        'contact'  => $contact,
                        'errors'   => $errors,
                        'messages' => $this->flash->getMessages(),
                    ]
                )
            );
            return $response;
        }
    }

    /**
     * @throws RuntimeException
     * @throws Twig\Error\Error
     */
    public function show(
        Request $request,
        ResponseInterface $response,
        int $id
    ): ResponseInterface {
        $contact = $this->getRepository()->find($id);
        $response->getBody()->write(
            $this->twig->render(
                'contact\show.html.twig',
                [
                    'contact'  => $contact,
                ]
            )
        );
        return $response;
    }

    /**
     * @throws RuntimeException
     * @throws Twig\Error\Error
     */
    public function edit(
        Request $request,
        ResponseInterface $response,
        int $id
    ): ResponseInterface {
        $contact = $this->getRepository()->find($id);
        $response->getBody()->write(
            $this->twig->render(
                'contact\edit.html.twig',
                [
                    'contact'  => $contact,
                ]
            )
        );
        return $response;
    }

    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws Twig\Error\Error
     */
    public function update(
        Request $request,
        ResponseInterface $response,
        int $id
    ): ResponseInterface {
        $form = (array) $request->getParsedBody();
        $contact = $this->getRepository()->find($id);
        assert($contact instanceof Domain\Contact);
        $contact->setFirst($form['first-name']);
        $contact->setLast($form['last-name']);
        $contact->setEmail($form['email']);
        $contact->setPhone($form['phone']);
        try {
            $violations = $this->validator->validate($contact);
            $errors = [];
            if (count($violations) > 0) {
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                throw new UnexpectedValueException('The form has failed validation');
            }

            $this->entityManager->persist($contact);
            $this->entityManager->flush();
            $this->flash->addMessage('info', 'Updated contact!');
            return $response
                ->withHeader('Location', '/contacts')
                ->withStatus(302);
        } catch (
            ORM\Exception\ORMException |
            ORM\ORMInvalidArgumentException |
            DBAL\Exception\UniqueConstraintViolationException |
            UnexpectedValueException $e
        ) {
            $this->flash->addMessageNow('danger', $e->getMessage());
            $response->getBody()->write(
                $this->twig->render(
                    'contact\edit.html.twig',
                    [
                        'contact'  => $contact,
                        'errors'   => $errors,
                        'messages' => $this->flash->getMessages(),
                    ]
                )
            );
            return $response;
        }
    }

    /**
     * @throws DBAL\Exception\UniqueConstraintViolationException
     * @throws InvalidArgumentException
     * @throws ORM\Exception\ORMException
     * @throws ORM\ORMInvalidArgumentException
     * @throws RuntimeException
     * @throws Twig\Error\Error
     */
    public function delete(
        Request $request,
        ResponseInterface $response,
        int $id
    ): ResponseInterface {
        $contact = $this->getRepository()->find($id);
        assert($contact instanceof Domain\Contact);
        $this->entityManager->remove($contact); // FIXME this can fail!
        $this->entityManager->flush();
        $this->flash->addMessage('info', 'Deleted contact!');
        return $response
            ->withHeader('Location', '/contacts')
            ->withStatus(302);
    }
}
