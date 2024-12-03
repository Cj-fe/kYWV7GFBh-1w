<script>
    import React from 'react';
import { User, Mail, BookOpen, GraduationCap, Lock, Shield } from 'lucide-react';

const AuthPage = () => {
  const [isSignIn, setIsSignIn] = React.useState(true);

  return (
    <div className="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-blue-50 to-indigo-50">
      <div className="w-full max-w-md bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-8 border border-white/20">
        <div className="flex justify-center mb-6">
          <div className="rounded-full bg-blue-100 p-3">
            <Shield className="w-8 h-8 text-blue-600" />
          </div>
        </div>
        
        <h1 className="text-3xl font-bold text-center mb-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-transparent bg-clip-text">
          Alumni Portal
        </h1>
        <p className="text-center text-gray-600 mb-8">Welcome back to your alumni community</p>

        <div className="flex gap-2 mb-8 bg-gray-100/50 p-1 rounded-lg">
          <button 
            onClick={() => setIsSignIn(true)}
            className={`flex-1 py-2.5 px-4 rounded-md transition-all duration-300 font-medium ${
              isSignIn 
                ? 'bg-white text-blue-600 shadow-sm' 
                : 'text-gray-600 hover:bg-white/50'
            }`}
          >
            Sign In
          </button>
          <button 
            onClick={() => setIsSignIn(false)}
            className={`flex-1 py-2.5 px-4 rounded-md transition-all duration-300 font-medium ${
              !isSignIn 
                ? 'bg-white text-blue-600 shadow-sm' 
                : 'text-gray-600 hover:bg-white/50'
            }`}
          >
            Sign Up
          </button>
        </div>

        {isSignIn ? (
          <form action="login_process.php" method="POST" className="space-y-4">
            <div className="relative group">
              <Mail className="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
              <input
                type="email"
                name="email"
                placeholder="Email"
                className="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-200 bg-gray-50/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                required
              />
            </div>
            <div className="relative group">
              <Lock className="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
              <input
                type="password"
                name="password"
                placeholder="Password"
                className="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-200 bg-gray-50/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                required
              />
            </div>
            <button
              type="submit"
              className="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 font-medium shadow-lg shadow-blue-500/20"
            >
              Sign In
            </button>
          </form>
        ) : (
          <form action="register_process.php" method="POST" className="space-y-4">
            <div className="relative group">
              <User className="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
              <input
                type="text"
                name="fullName"
                placeholder="Full Name"
                className="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-200 bg-gray-50/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                required
              />
            </div>
            <div className="relative group">
              <Mail className="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
              <input
                type="email"
                name="email"
                placeholder="Email"
                className="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-200 bg-gray-50/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                required
              />
            </div>
            <div className="relative group">
              <BookOpen className="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
              <input
                type="text"
                name="department"
                placeholder="Department"
                className="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-200 bg-gray-50/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                required
              />
            </div>
            <div className="relative group">
              <GraduationCap className="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
              <input
                type="text"
                name="graduationYear"
                placeholder="Graduation Year"
                className="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-200 bg-gray-50/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                required
              />
            </div>
            <div className="relative group">
              <Lock className="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
              <input
                type="password"
                name="password"
                placeholder="Password"
                className="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-200 bg-gray-50/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                required
              />
            </div>
            <button
              type="submit"
              className="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 font-medium shadow-lg shadow-blue-500/20"
            >
              Sign Up
            </button>
          </form>
        )}

        <p className="text-center text-sm text-gray-600 mt-6 flex items-center justify-center gap-2">
          <Shield className="w-4 h-4" />
          Protected by reCAPTCHA and subject to our Privacy Policy
        </p>
      </div>
    </div>
  );
};

export default AuthPage;
</script>