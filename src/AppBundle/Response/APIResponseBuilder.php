<?php

namespace AppBundle\Response;

use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class APIResponseBuilder.
 */
class APIResponseBuilder
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param mixed  $body
     * @param string $name
     * @param int    $statusCode
     *
     * @return APIResponse
     */
    public function buildSuccessResponse($body, $name, $statusCode = 200)
    {
        $response = new APIResponse($statusCode);
        $response->addToBody($body, $name);

        return $response;
    }

    /**
     * @param int $statusCode
     *
     * @return APIResponse
     */
    public function buildEmptySuccessResponse($statusCode = 200)
    {
        $response = new APIResponse($statusCode);

        return $response;
    }

    /**
     * @param mixed  $body
     * @param string $name
     *
     * @return APIResponse
     */
    public function buildNotFoundResponse($message)
    {
        $response = new APIResponse(404, 'error');
        $response->addMessage($message);

        return $response;
    }

    /**
     * @param mixed  $body
     * @param string $name
     *
     * @return APIResponse
     */
    public function buildBadRequestResponse($message)
    {
        $response = new APIResponse(400, 'error');
        $response->addMessage($message);

        return $response;
    }

    /**
     * @param string $statusCode
     * @param string $status
     *
     * @return APIResponse
     */
    public function buildResponse($statusCode, $status)
    {
        $response = new APIResponse($statusCode, $status);

        return $response;
    }

    /**
     * @param mixed  $body
     * @param string $name
     *
     * @return APIResponse
     */
    public function buildServerErrorResponse($message = null)
    {
        $response = new APIResponse(500, 'error');
        if ($message !== null) {
            $response->addMessage($message);
        }

        return $response;
    }

    /**
     * @param mixed  $body
     * @param string $name
     *
     * @return APIResponse
     */
    public function buildFormErrorResponse(Form $form)
    {
        $message = (string) $form->getErrors(true, true);
        $response = new APIResponse(400, 'error');
        foreach ($this->getFormFieldErrors($form) as $field => $error) {
            $error = str_replace('This form', 'The data', $error);
            if ($field === '') {
                $response->addMessage($error);
                continue;
            }
            $response->addMessage(sprintf('%s: %s', $field, $error));
        }

        return $response;
    }

    /**
     * @param Form $form
     *
     * @return array
     */
    protected function getFormFieldErrors(Form $form)
    {
        $errorsReturn = array();
        $constraintViolationList = $this->validator->validate($form);
        foreach ($constraintViolationList as $error) {
            $property = str_replace('children[', '', $error->getPropertyPath());
            $property = str_replace(']', '', $property);
            $property = str_replace('.data', '', $property);
            $property = str_replace('data.', '', $property);
            $errorsReturn[$property] = $error->getMessage();
        }

        return $errorsReturn;
    }
}
