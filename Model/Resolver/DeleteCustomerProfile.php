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
use Magento\Framework\Api\Data\ImageContentInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Serialize\Serializer\Json;
use MageINIC\CustomerProfile\Model\CustomerRepository;

/**
 * Class Of Delete Customer Profile
 */
class DeleteCustomerProfile implements ResolverInterface
{
    /**
     * @var GetCustomer
     */
    protected GetCustomer $getCustomer;

    /**
     * @var CustomerRepository
     */
    private CustomerRepository $customerRepository;

    /**
     * @var ImageContentInterfaceFactory
     */
    protected ImageContentInterfaceFactory $imageContentInterfaceFactory;

    /**
     * @var Json
     */
    protected Json $serializer;

    /**
     * Delete customer profile constructor
     * 
     * @param CustomerRepository $customerRepository
     * @param GetCustomer $getCustomer
     * @param ImageContentInterfaceFactory $imageContentInterfaceFactory
     * @param Json $serializer
     */
    public function __construct(
        CustomerRepository           $customerRepository,
        GetCustomer                  $getCustomer,
        ImageContentInterfaceFactory $imageContentInterfaceFactory,
        Json                         $serializer
    ) {
        $this->customerRepository = $customerRepository;
        $this->getCustomer = $getCustomer;
        $this->imageContentInterfaceFactory = $imageContentInterfaceFactory;
        $this->serializer = $serializer;
    }

    /**
     * Delete Customer Profile Resolver
     *
     * @param Field $field
     * @param int $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws GraphQlAuthorizationException
     * @throws GraphQlNoSuchEntityException|LocalizedException
     */
    public function resolve(
        Field       $field,
        $context,
        ResolveInfo $info,
        array       $value = null,
        array       $args = null
    ) {
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }
        $customerId = (int)$context->getUserId();
        $result = $this->customerRepository->removeProfileUpload($customerId);
        $profile['success_message'] = $result;
        return $profile;
    }
}
