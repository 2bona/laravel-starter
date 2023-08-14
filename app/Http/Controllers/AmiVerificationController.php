<?php
namespace App\Http\Controllers;
use Aws\Credentials\CredentialProvider;
use Illuminate\Http\Request;
use Aws\Ec2\Ec2Client;
class AmiVerificationController extends Controller
{
    public function verify()
    {
        $provider = CredentialProvider::defaultProvider();

        $ec2Client = new Ec2Client([
            'region' => 'us-east-2', // Replace with your desired AWS region
            'version' => 'latest',
            'credentials' => $provider
        ]);

        // Retrieve the instance identity document from the metadata service
        $instanceIdentityDocument = $this->getInstanceIdentityDocument();

        // Verify that the instance is running your AMI
        if ($this->isRunningYourAmi($ec2Client, $instanceIdentityDocument)) {
            return response('Instance is running your AMI', 200);
        }

        return response('Instance is not running your AMI', 403);
    }

    protected function getInstanceIdentityDocument()
    {
        // Retrieve the instance identity document using the AWS metadata service
        $response = file_get_contents('http://169.254.169.254/latest/dynamic/instance-identity/document');
        return json_decode($response, true);
    }

    protected function isRunningYourAmi($ec2Client, $instanceIdentityDocument)
    {
        // Replace 'your-ami-id' with the expected AMI ID
        $expectedAmiId = 'ami-024e6efaf93d85776';

        // Describe the instance using the instance ID from the identity document
        $response = $ec2Client->describeInstances([
            'InstanceIds' => [$instanceIdentityDocument['instanceId']],
        ]);

        // Verify that the instance is running your specified AMI
        $instances = $response['Reservations'][0]['Instances'];
        foreach ($instances as $instance) {
            if ($instance['ImageId'] === $expectedAmiId) {
                return true;
            }
        }

        return false;
    }
}
