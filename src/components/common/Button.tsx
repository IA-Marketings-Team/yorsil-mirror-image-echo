
// This is now just a re-export of the UI Button component for backward compatibility
import { Button as UIButton, ButtonProps } from "@/components/ui/button";

const Button = (props: ButtonProps) => {
  return <UIButton {...props} />;
};

export default Button;
