<?php

declare(strict_types=1);

namespace App\Mail;

use Arthem\Bundle\RabbitBundle\Log\LoggableTrait;
use Psr\Log\LoggerAwareInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;

class Mailer implements LoggerAwareInterface
{
    use LoggableTrait;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var string
     */
    private $from;

    /**
     * @var Environment
     */
    private $templating;

    public function __construct(Environment $templating, MailerInterface $mailer, string $from)
    {
        $this->mailer = $mailer;
        $this->from = $from;
        $this->templating = $templating;
    }

    public function send(string $to, string $template, array $parameters): void
    {
        $email = (new Email())
            ->from($this->from)
            ->to($to)
            ->subject($this->renderSubject($template, $parameters))
            ->html($this->renderView($template, $parameters));

        $this->logger->info(sprintf('Send mail "%s" to "%s"', $template, $to));
        $this->mailer->send($email);
    }

    private function renderView(string $template, array $parameters): string
    {
        return $this->renderFile($template, $parameters);
    }

    private function renderSubject(string $template, array $parameters):string
    {
        return $this->renderFile($template.'_subject', $parameters);
    }

    private function renderFile(string $file, array $parameters): string
    {
        return $this->templating->render('mail/'.$file.'.html.twig', $parameters);
    }

    public function validateParameters(string $template, array $parameters): void
    {
        try {
            $this->renderSubject($template, $parameters);
            $this->renderView($template, $parameters);
        } catch (LoaderError $e) {
            dump($e->getMessage());
            throw new BadRequestHttpException(sprintf('Undefined template "%s"', $template));
        } catch (RuntimeError $e) {
            if (1 === preg_match('#^Variable "([^"]+)" does not exist.$#', $e->getMessage(), $regs)) {
                throw new BadRequestHttpException(sprintf('Missing parameter "%s"', $regs[1]));
            }

            throw $e;
        }
    }
}
