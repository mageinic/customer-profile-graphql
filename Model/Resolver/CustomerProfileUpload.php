<?php
/**
 * MageINIC
 * Copyright (C) 2023 MageINIC <support@mageinic.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package MageINIC_CustomerProfileGraphQl
 * @copyright Copyright (c) 2023 MageINIC (https://www.mageinic.com/)
 * @license https://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

namespace MageINIC\CustomerProfileGraphQl\Model\Resolver;

use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\Api\Data\ImageContentInterface;
use Magento\Framework\Api\Data\ImageContentInterfaceFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Serialize\Serializer\Json;
use MageINIC\CustomerProfile\Model\CustomerRepository;
use Magento\Framework\Filesystem;

/**
 * Class Customer Profile GraphQl
 */
class CustomerProfileUpload implements ResolverInterface
{
    public const COMMA = ',';

    /**
     * @var CustomerRepository
     */
    private CustomerRepository $customerRepository;

    /**
     * @var GetCustomer
     */
    public GetCustomer $getCustomer;

    /**
     * @var ImageContentInterfaceFactory
     */
    protected ImageContentInterfaceFactory $imageContentInterfaceFactory;

    /**
     * @var Json
     */
    public Json $serializer;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * Create customer profile constructor
     *
     * @param CustomerRepository $customerRepository
     * @param GetCustomer $getCustomer
     * @param Filesystem $filesystem
     * @param ImageContentInterfaceFactory $imageContentInterfaceFactory
     * @param Json $serializer
     * @param RequestInterface $request
     */
    public function __construct(
        CustomerRepository           $customerRepository,
        GetCustomer                  $getCustomer,
        Filesystem                   $filesystem,
        ImageContentInterfaceFactory $imageContentInterfaceFactory,
        Json                         $serializer,
        RequestInterface             $request,
    ) {
        $this->customerRepository = $customerRepository;
        $this->getCustomer = $getCustomer;
        $this->imageContentInterfaceFactory = $imageContentInterfaceFactory;
        $this->serializer = $serializer;
        $this->filesystem = $filesystem;
        $this->request = $request;
    }

    /**
     * Customer Profile Upload Resolver
     *
     * @param Field $field
     * @param int $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws GraphQlAuthorizationException
     * @throws GraphQlInputException
     */
    public function resolve(
        Field       $field,
        $context,
        ResolveInfo $info,
        array       $value = null,
        array       $args = null
    ) {
        if (!isset($args['input']['base64_encoded_data'])) {
            throw new GraphQlInputException(__('Please fill all filed'));
        }
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }
        $customerId = (int)$context->getUserId();
        $result = $this->customerProfileUpload($args, $customerId);
        $profile['profile_url'] = $result;
        return $profile;
    }

    /**
     * Customer Profile Upload Function
     *
     * @param array $args
     * @param int $customerId
     * @return mixed|string
     * @throws InputException
     * @throws InputMismatchException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function customerProfileUpload(array $args, int $customerId): mixed
    {
        try {
            $valid = $this->validatedParams($args);
            if ($valid) {
                $data = $valid['input'];
                $imageInterface = $this->imageContentInterfaceFactory->create();
                $imageInterface->setBase64EncodedData($data[ImageContentInterface::BASE64_ENCODED_DATA]);
                $imageInterface->setType($data[ImageContentInterface::TYPE]);
                $imageInterface->setName($data[ImageContentInterface::NAME]);
            }
            $result = $this->customerRepository->save($imageInterface, $customerId);
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException($e->getMessage());
        }
        return $result;
    }

    /**
     * Validate Params Function
     *
     * @param array $args
     * @return array
     * @throws LocalizedException
     */
    private function validatedParams(array $args): array
    {
        $fileName =  $args['input']['base64_encoded_data'] ?? '';
        $imageName =  $args['input']['name'] ?? '';
        $fileType =  $args['input']['type'] ?? '';
        $data =[
            'base64_encoded_data'=> $fileName,
            'name'=> $imageName,
            'type'=> $fileType
        ];
        if (!array_key_exists(ImageContentInterface::BASE64_ENCODED_DATA, $data) ||
            trim($data[ImageContentInterface::BASE64_ENCODED_DATA]) === ''
        ) {
            throw new LocalizedException(__('Enter the base64 encoded data key and value try again.'));
        }
        if (!array_key_exists(ImageContentInterface::NAME, $data) ||
            trim($data[ImageContentInterface::NAME]) === ''
        ) {
            throw new LocalizedException(__('Enter the name key and value try again.'));
        }
        if (!array_key_exists(ImageContentInterface::TYPE, $data) ||
            trim($data[ImageContentInterface::TYPE]) === ''
        ) {
            throw new LocalizedException(__('Enter the type key and value try again.'));
        }
        return $args;
    }
}
